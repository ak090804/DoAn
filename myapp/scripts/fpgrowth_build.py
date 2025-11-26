#!/usr/bin/env python3
"""
Simple FP-Growth builder script using mlxtend.

Usage: python scripts/fpgrowth_build.py transactions.csv output.json --min_support 0.02 --min_confidence 0.3

Input: CSV where each row is a transaction: comma-separated product_variant_ids
Output: JSON array of rules: {"antecedent": [ids], "consequent": [ids], "support":..., "confidence":..., "lift":...}
"""
import json
import argparse
import pandas as pd
from mlxtend.preprocessing import TransactionEncoder
from mlxtend.frequent_patterns import fpgrowth, association_rules


def read_transactions(path):
    transactions = []
    with open(path, 'r', encoding='utf8') as f:
        for line in f:
            row = [x.strip() for x in line.strip().split(',') if x.strip()]
            if row:
                transactions.append(row)
    return transactions


def build(args):
    tx = read_transactions(args.transactions)
    te = TransactionEncoder()
    te_ary = te.fit(tx).transform(tx)
    df = pd.DataFrame(te_ary, columns=te.columns_)

    freq = fpgrowth(df, min_support=args.min_support, use_colnames=True)
    rules = association_rules(freq, metric='confidence', min_threshold=args.min_confidence)

    # convert sets to lists of ints when possible
    out = []
    for _, r in rules.iterrows():
        antecedent = [int(x) for x in r['antecedents']] if len(r['antecedents']) > 0 else []
        consequent = [int(x) for x in r['consequents']] if len(r['consequents']) > 0 else []
        out.append({
            'antecedent': antecedent,
            'consequent': consequent,
            'support': float(r['support']),
            'confidence': float(r['confidence']),
            'lift': float(r['lift'])
        })

    # optionally limit number of rules
    if args.top:
        out = sorted(out, key=lambda x: (x['confidence'], x['support']), reverse=True)[:args.top]

    with open(args.output, 'w', encoding='utf8') as f:
        json.dump(out, f, ensure_ascii=False, indent=2)


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument('transactions')
    parser.add_argument('output')
    parser.add_argument('--min_support', type=float, default=0.02)
    parser.add_argument('--min_confidence', type=float, default=0.3)
    parser.add_argument('--top', type=int, default=0)
    args = parser.parse_args()
    build(args)


if __name__ == '__main__':
    main()
