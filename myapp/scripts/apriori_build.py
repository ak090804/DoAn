# scripts/apriori_build.py
import pandas as pd
import json
from apyori import apriori
import argparse
from collections import defaultdict

parser = argparse.ArgumentParser(description="Build Apriori 1→1 rules with top N consequents per antecedent")
parser.add_argument("input", help="CSV file of transactions")
parser.add_argument("output", help="JSON output file")
parser.add_argument("--min_support", type=float, default=0.005)
parser.add_argument("--min_confidence", type=float, default=0.2)
parser.add_argument("--min_lift", type=float, default=3.0)
parser.add_argument("--top", type=int, default=3, help="Max consequents per antecedent (0 = no limit)")
args = parser.parse_args()

# Load data
df = pd.read_csv(args.input, header=None)
transactions = df.apply(lambda row: [str(item).strip() for item in row.dropna() if str(item).strip()], axis=1).tolist()

# Run Apriori
rules_gen = apriori(
    transactions,
    min_support=args.min_support,
    min_confidence=args.min_confidence,
    min_lift=args.min_lift,
    min_length=2
)

# Group by antecedent
antecedent_dict = defaultdict(list)
seen_pairs = set()

for rule in rules_gen:
    support = rule.support
    for stat in rule.ordered_statistics:
        antecedents = [str(a) for a in stat.items_base]
        consequents = [str(c) for c in stat.items_add]
        
        if not antecedents or not consequents:
            continue
            
        confidence = stat.confidence
        lift = stat.lift

        for ante in antecedents:
            for cons in consequents:
                pair = (ante, cons)
                if pair in seen_pairs:
                    continue
                seen_pairs.add(pair)

                antecedent_dict[ante].append({
                    "antecedent": ante,
                    "consequent": cons,
                    "support": support,
                    "confidence": confidence,
                    "lift": lift
                })

# Get top N per antecedent
final_rules = []
top_n = args.top if args.top > 0 else None  # None = không giới hạn

for ante, items in antecedent_dict.items():
    items.sort(key=lambda x: x["confidence"], reverse=True)
    for item in items[:top_n]:
        final_rules.append(item)

# Sort tổng thể (tùy chọn)
final_rules.sort(key=lambda x: x["confidence"], reverse=True)

# Save JSON (danh sách phẳng - tương thích với Laravel)
with open(args.output, "w", encoding="utf-8") as f:
    json.dump(final_rules, f, indent=2, ensure_ascii=False)