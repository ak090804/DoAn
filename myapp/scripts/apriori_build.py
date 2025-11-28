import pandas as pd
import json
from apyori import apriori
import argparse

# ======= ARG PARSER =======
parser = argparse.ArgumentParser()
parser.add_argument("input", help="CSV file of transactions")
parser.add_argument("output", help="JSON output file")
parser.add_argument("--min_support", type=float, default=0.005)
parser.add_argument("--min_confidence", type=float, default=0.2)
parser.add_argument("--min_lift", type=float, default=3)
parser.add_argument("--top", type=int, default=0)
args = parser.parse_args()

# ======= LOAD TRANSACTIONS =======
df = pd.read_csv(args.input, header=None)
transactions = df.apply(lambda r: r.dropna().astype(str).tolist(), axis=1).tolist()

# ======= RUN APRIORI =======
rules_gen = apriori(
    transactions,
    min_support=args.min_support,
    min_confidence=args.min_confidence,
    min_lift=args.min_lift,
    min_length=2
)

results_list = list(rules_gen)
rules = []
seen = set()

# ======= CHUYỂN LUẬT THÀNH A→B, A→C =======
for rule in results_list:
    for stat in rule.ordered_statistics:
        antecedent = list(stat.items_base)
        consequent = list(stat.items_add)
        if not antecedent or not consequent:
            continue

        # Tách từng B trong C
        for B in consequent:
            pair = (tuple(antecedent), B)  # lưu tuple để kiểm tra trùng
            if pair in seen:
                continue
            seen.add(pair)

            rules.append({
                "antecedent": list(antecedent) if len(antecedent) > 1 else antecedent[0],
                "consequent": B,
                "support": rule.support,
                "confidence": stat.confidence,
                "lift": stat.lift
            })

# ======= SORT VÀ APPLY TOP =======
rules.sort(key=lambda x: x["confidence"], reverse=True)
if args.top > 0:
    rules = rules[:args.top]

# ======= SAVE JSON =======
with open(args.output, "w", encoding="utf8") as f:
    json.dump(rules, f, indent=2, ensure_ascii=False)


