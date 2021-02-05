import numpy as np
import pandas as pd
from textblob import TextBlob
PUNCTUATIONS = '!"#$%&\()*+,-./:;<=>?@[\\]^_{|}~'


class Cleaner:
    def __main__(self, DataFrame):
        self.df = DataFrame

    def separateEmptyReview(self):
        self.empty_df = self.df[self.df.review_text == ""].copy()
        self.df = self.df[self.df.review_text != ""].copy()
        return len(DataFrame.emptyReviews)

    def remove_translated(self):
        def __function(text):
            text = text.replace("(Translated by Google)", "")
            return text[:text.find("(Original)")].strip()
        self.__apply_to_review(__function)

    def remove_punctuation(self):
        def __function(input_text):
            return input_text.translate(str.maketrans('', '', PUNCTUATIONS))
        self.__apply_to_review(__function)

    def lemmatize(self):
        def __function(input_text):
            textBlob = TextBlob(text)
            tag_dict = {"J": 'a', "N": 'n', "V": 'v', "R": 'r'}
            words_and_tags = [(w, tag_dict.get(pos[0], 'n'))
                              for w, pos in textBlob.tags]
            lemmatized_list = [wd.lemmatize(tag) for wd, tag in words_and_tags]
            return ' '.join(lemmatized_list)
        self.__apply_to_review(__function)

    def expand_contracts(self):
        translation = {
            r"won\'t": " will not", r"can\'t": " can not",
            r"n\'t": " not", r"\'re": " are",
            r"\'s": " is", r"\'d": " would",
            r"\'ll": " will", r"\'t": " not",
            r"\'ve": " have", r"\'m": " am"
        }

        def __function(input_text):
            for key, value in translation.items():
                text = re.sub(key, value, input_text)
            return input_text
        self.__apply_to_review(__function)

    def tokenizer(self):
        def __function(input_text):
            textBlob = TextBlob(text)
            return blob_object.lower().words
        self.df.review_text = self.df.apply(
            lambda x: tokenizer(x.review_text), axis=1)

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
