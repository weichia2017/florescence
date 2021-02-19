while read requirement; do conda install -c condaforge --yes $requirement; done < conda.txt
while read requirement; do pip install --yes $requirement; done < pip.txt 

python -m spacy download en_core_web_sm