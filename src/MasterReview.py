import numpy as np
import pandas as pd
# For sentimentsForStore()
from vaderSentiment.vaderSentiment import SentimentIntensityAnalyzer
# For WordMap()
import spacy
from textblob import TextBlob

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
        self.__lemmatize()
        nlp = spacy.load("en_core_web_sm", disable=['ner'])
        all_pairs = pd.DataFrame(columns=['NOUN', 'ADJ'])
        chunks = self.__chunkify(self.df,500)
        for chunk in chunks:
            doc = nlp(chunk)
            for token in doc:
                if token.pos_ == 'ADJ' and token.dep_ == "amod" and token.head != None and token.head.pos_ in ['PROPN', 'NOUN']:
                    row = {'NOUN': token.head.text.lower(), 'ADJ': token.text.lower()}
                    all_pairs = all_pairs.append(row, ignore_index=True)
        top_noun = all_pairs.groupby(['NOUN']).size().reset_index(name='COUNT').sort_values(['COUNT'], ascending=False).head(num_of_noun)
        filtered_nouns = all_pairs[all_pairs.NOUN.isin(top_noun.NOUN.to_list())]
        noun_adj_ranking = filtered_nouns.groupby(['NOUN','ADJ']).size().reset_index(name='COUNT').sort_values(['COUNT'], ascending=False)
        top_noun_adj_pair = noun_adj_ranking.groupby(['NOUN']).apply(lambda x: x.nlargest(num_of_adj_each,['COUNT'], keep='first')).reset_index(drop=True).sort_values(['NOUN','COUNT'], ascending=False).reset_index(drop=True)
        return top_noun_adj_pair

    def sentiment_scores(self):
        df = self.df
        self.__separateEmptyReview()
        self.__remove_translated()
        def vaderSent(text):
            analyzer = SentimentIntensityAnalyzer()
            vs = analyzer.polarity_scores(text)
            return f"{vs['neg']},{vs['neu']},{vs['pos']},{vs['compound']}"
        df['vader_score'] = np.vectorize(vaderSent)(df.review_text)
        df[['neg','neu','pos','compound']] = df.vader_score.str.split(",",expand=True)
        df[['neg','neu','pos','compound']] = df[['neg','neu','pos','compound']].astype(float)
        return df[['review_date', 'neg', 'neu', 'pos', 'compound']]

    def __chunkify(self, df, size):
        df = df['review_text']
        max_size = len(df.index)
        if size > max_size:
            return [' '.join(df)]
        chunks = []
        start, end = 0, size
        while start < max_size:
            chunks.append(' '.join(df.iloc[start:end]))
            start += size
            end += size
        return chunks
    
    def __lemmatize(self):
        def __function(input_text):
            textBlob = TextBlob(input_text)
            tag_dict = {"J": 'a', "N": 'n', "V": 'v', "R": 'r'}
            words_and_tags = [(w, tag_dict.get(pos[0], 'n'))
                              for w, pos in textBlob.tags]
            lemmatized_list = [wd.lemmatize(tag) for wd, tag in words_and_tags]
            return ' '.join(lemmatized_list)
        self.__apply_to_review(__function)

    def __separateEmptyReview(self):
        self.df = self.df[self.df.review_text != ""].copy()

    def __remove_translated(self):
        def __function(input_text):
            input_text = input_text.replace("(Translated by Google)", "")
            return input_text[:input_text.find("(Original)")].strip()
        self.__apply_to_review(__function)

    def __apply_to_review(self, func):
        self.df.review_text = np.vectorize(func)(self.df.review_text)
        
