
import numpy as np
import pandas as pd
from textblob import TextBlob
PUNCTUATIONS = '!"#$%&\()*+,-./:;<=>?@[\\]^_{|}~'

class Cleaner:
    def __main__(self, DataFrame):
        self.activity = []
        self.df = DataFrame
    
    def seperateEmptyReview(self):
        self.empty_df = self.df[self.df.review_text==""].copy()
        self.df = self.df[self.df.review_text!=""].copy()
        return len(DataFrame.emptyReviews)

    def remove_translated(self):
        def __function(text):
            text = text.replace("(Translated by Google)","")
            return text[:text.find("(Original)")].strip()
        self.__apply_to_review(__function)

    def remove_punctuation(self):
        def __function(input_text):
            input_text.translate(str.maketrans('', '', PUNCTUATIONS))
        self.__apply_to_review(__function)

    def lemmatize(self):
        def __function(input_text):
            textBlob = TextBlob(text)
            tag_dict = {"J": 'a', "N": 'n', "V": 'v', "R": 'r'}
            words_and_tags = [(w, tag_dict.get(pos[0], 'n')) for w, pos in textBlob.tags]    
            lemmatized_list = [wd.lemmatize(tag) for wd, tag in words_and_tags]
            return ' '.join(lemmatized_list)
        self.__apply_to_review(__function)

    def tokenizer(self):
        def __function(input_text):
            textBlob = TextBlob(text)
            return blob_object.lower().words
        self.__apply_to_review(__function)

    def __apply_to_review(self, func):
        df.review_text = np.vectorize(func)(self.df.review_text)

    def get_empty_df(self):
        try:
            self.empty_df
        except NameError:
            return None
        else:
            return self.empty_df

    def get_df(self):
        return self.df
    
