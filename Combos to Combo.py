import glob
import os
import re


# PUT THE SCRIPT IN THE FOLDER AND EXECUTE IT - IT SHOULD DO THE WORK


os.chdir(os.getcwd())
files = glob.glob('*.txt')

mailpassReg = r'([a-z0-9._-])+@([a-z0-9.-])+\.([a-z0-9-])+(\.[a-z0-9])?:\S+'


# ADD files IN A SET
combos = set()
for combolist in files:
    with open(combolist, 'r') as f:
        for line in f:
            try:
                mailpass = re.search(mailpassReg, line).group(0)
                combos.add(mailpass)
            except:
                pass

# INSERT SETS INTO THE OUTPUT FILE
with open("output.txt", 'a') as f:
    for combo in combos:
        f.write(combo + "\n")