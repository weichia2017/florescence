while read requirement; do conda install --yes $requirement; done < conda.txt
while read requirement; do pip install --yes $requirement; done < pip.txt 