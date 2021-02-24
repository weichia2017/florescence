import numpy as np
import pandas as pd
from vaderSentiment.vaderSentiment import SentimentIntensityAnalyzer

class Master:
    def __init__(self, DataFrame):
        self.df = DataFrame

    def __enter__(self):
        return self

    def __exit__(self, exc_type, exc_value, tb):
        return True
    
    def sentimentsForStore(self):
        df = self.df
        self.__separateEmptyReview()
        self.__remove_translated()
        def vaderSent(text):
            analyzer = SentimentIntensityAnalyzer()
            vs = analyzer.polarity_scores(text)
            return f"{vs['neg']}, {vs['neu']}, {vs['pos']}, {vs['compound']}"
        df['vader_score'] = np.vectorize(vaderSent)(df.review_text)
        df[['neg','neu','pos','compound']] = df.vader_score.str.split(',', expand=True)
        return df[['review_date', 'neg', 'neu', 'pos', 'compound']]
    
    def __separateEmptyReview(self):
        self.df = self.df[self.df.review_text != ""].copy()

    def __remove_translated(self):
        def __function(input_text):
            input_text = input_text.replace("(Translated by Google)", "")
            return input_text[:input_text.find("(Original)")].strip()
        self.__apply_to_review(__function)

    def __apply_to_review(self, func):
        self.df.review_text = np.vectorize(func)(self.df.review_text)
        
