# scripts/apriori_build.py
import pandas as pd
import json
from apyori import apriori
import argparse
from collections import defaultdict

parser = argparse.ArgumentParser(description="Build Apriori recommendation rules")
parser.add_argument("input", help="CSV file of transactions")
parser.add_argument("output", help="JSON output file")
parser.add_argument("--min_support", type=float, default=0.005)
parser.add_argument("--min_confidence", type=float, default=0.2)
parser.add_argument("--min_lift", type=float, default=3.0)
parser.add_argument("--top", type=int, default=3, help="Max recommended products per product_id (0 = unlimited)")
args = parser.parse_args()

# Load CSV: mỗi dòng là 1 list sản phẩm
df = pd.read_csv(args.input, header=None)
transactions = df.apply(
    lambda row: [str(item).strip() for item in row.dropna() if str(item).strip()],
    axis=1
).tolist()

# Run apriori
rules_gen = apriori(
    transactions,
    min_support=args.min_support,
    min_confidence=args.min_confidence,
    min_lift=args.min_lift,
    min_length=2
)

# Gom nhóm theo product_id (antecedent)
grouped_rules = defaultdict(list)
seen_pairs = set()

for rule in rules_gen:
    rule_support = rule.support

    for stat in rule.ordered_statistics:
        antecedents = [str(a) for a in stat.items_base]
        consequents = [str(c) for c in stat.items_add]

        if not antecedents or not consequents:
            continue

        confidence = stat.confidence
        lift = stat.lift

        for ante in antecedents:
            for cons in consequents:
                key = (ante, cons)
                if key in seen_pairs:
                    continue
                seen_pairs.add(key)

                grouped_rules[ante].append({
                    "product_id": ante,
                    "recommended_product_id": cons,
                    "support": rule_support,
                    "confidence": confidence,
                    "lift": lift
                })

# Lấy top-N theo sconfidencecore
final_rules = []
top_n = args.top if args.top > 0 else None

for product_id, items in grouped_rules.items():
    items.sort(key=lambda x: x["confidence"], reverse=True)
    final_rules.extend(items[:top_n])

# Sort tổng thể (không bắt buộc)
final_rules.sort(key=lambda x: x["confidence"], reverse=True)

# Ghi JSON
with open(args.output, "w", encoding="utf-8") as f:
    json.dump(final_rules, f, indent=2, ensure_ascii=False)
