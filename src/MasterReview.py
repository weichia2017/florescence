import numpy as np
import pandas as pd
from vaderSentiment.vaderSentiment import SentimentIntensityAnalyzer
import spacy

class Master:
    def __init__(self, DataFrame):
        self.df = DataFrame

    def __enter__(self):
        return self

    def __exit__(self, exc_type, exc_value, tb):
        return True
    
    def adj_noun_pairs(self, num_of_noun = 10, num_of_adj_each = 3):
        self.__separateEmptyReview()
        self.__remove_translated()
        df = self.df.reset_index()
        nlp = spacy.load("en_core_web_sm", disable=['ner'])
        lemmatizer = nlp.get_pipe("lemmatizer")
        def spcy(row):
            all_pairs = pd.DataFrame(columns=['review_id','noun', 'adj'])
            doc = nlp(row.review_text)
            for token in doc:
                if token.pos_ == 'ADJ' and token.dep_ == "amod" and token.head != None and token.head.pos_ in ['PROPN', 'NOUN'] and token.text != "-":
                    new_row = {'review_id': row.review_id, 'noun': token.head.lemma_.lower(), 'adj': token.lemma_.lower()}
                    all_pairs = all_pairs.append(new_row, ignore_index=True)
            return all_pairs
        all_pairs = df.apply(spcy, axis=1)
        all_pairs = pd.concat(all_pairs.to_list())
        top_noun = all_pairs.groupby(['noun']).size().reset_index(name='count').sort_values(['count'], ascending=False).head(num_of_noun)
        filtered_nouns = all_pairs[all_pairs.noun.isin(top_noun.noun.to_list())]
        noun_top_adj_ranking = filtered_nouns.groupby(['noun','adj']).size().reset_index(name='count').sort_values(['count'], ascending=False)
        filtered_adj = filtered_nouns[filtered_nouns.adj.isin(noun_top_adj_ranking.adj.to_list())]
        pairs = filtered_adj.groupby(['noun','adj'])['review_id'].apply(', '.join).reset_index()
        pairs['review_id'] = pairs.review_id.apply(lambda x: x.split(","))
        pairs['count'] = pairs.review_id.apply(lambda x: len(x))
        top_noun_adj_pair = pairs.groupby(['noun']).apply(lambda x: x.nlargest(num_of_adj_each,['count'], keep='first')).reset_index(drop=True).sort_values(['noun','count'], ascending=False).reset_index(drop=True)
        return top_noun_adj_pair[['noun', 'adj', 'review_id']]
    
    def sentiment_scores(self):
        self.__separateEmptyReview()
        self.__remove_translated()
        df = self.df.reset_index()
        def vaderSent(text):
            analyzer = SentimentIntensityAnalyzer()
            vs = analyzer.polarity_scores(text)
            return f"{vs['neg']},{vs['neu']},{vs['pos']},{vs['compound']}"
        df['vader_score'] = np.vectorize(vaderSent)(df.review_text)
        df[['neg','neu','pos','compound_score']] = df.vader_score.str.split(",",expand=True)
        df[['neg','neu','pos','compound_score']] = df[['neg','neu','pos','compound_score']].astype(float)
        return df[['review_id', 'review_date', 'review_text', 'compound_score']]

    def __separateEmptyReview(self):
        self.df = self.df[self.df.review_text != ""].copy()

    def __remove_translated(self):
        def __function(input_text):
            input_text = input_text.replace("(Translated by Google)", "")
            original_index = input_text.find("(Original)")
            if original_index == -1:
                return input_text.strip()
            return input_text[:original_index].strip()
        self.df.review_text = np.vectorize(__function)(self.df.review_text)

    def __apply_to_review(self, func):
        self.df.review_text = np.vectorize(func)(self.df.review_text)
        
