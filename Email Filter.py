#!/usr/bin/python
from colorama import Fore, Back, Style
import re
import argparse


# Create the parser
parser = argparse.ArgumentParser()

# Add arguments
parser.add_argument('mailist', type=str, help='the mailist name or path')
parser.add_argument('-f', type=str, nargs='+',
                    help='the domains you want to filter eg: @yahoo @gmail...')

# Parse the argument
args = parser.parse_args()

# PREPARE RESULTS FILES
results = {
    "valid": open('valid.txt', 'a'),
    "invalid": open('invalid.txt', 'a')
}
for to_filter in args.f:
    results[f"{to_filter}"] = open(f'{to_filter}.txt', 'a')


# EMAIL REGEX
email_regex = '^[a-z0-9]+[\._]?[a-z0-9]+[@]\w+[.]\w{2,3}$'


# OPEN MAILIST
mailist_open = open(args.mailist, 'r')

# READ MAILIST LINE BY LINE
lines = mailist_open.readlines()

# CHECK IF IT IS AN EMAIL

for line in lines:
    if (re.search(email_regex, line.strip())):
        results["valid"].write(line)
        for to_filter in args.f:
            if to_filter in line:
                print(Fore.GREEN + '[' + to_filter.upper() + '] ' + line)
                results[to_filter].write(line)

    else:
        print(Fore.RED + '[-]' + line)
        results["invalid"].write(line)
