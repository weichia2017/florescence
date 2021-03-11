import numpy as np
import pandas as pd
import re
from textblob import TextBlob
PUNCTUATIONS = '!"#$%&\()*+,-./:;<=>?@[\\]^_{|}~'


class Cleaner:
    """Cleaner module for Pre Processing.

    This module will deal with pre-processing of the Review Dataframe.
    Initalizing the object with a Dataframe (from the DatabaseAccess Module)
    and using get_df() to retrieve the Dataframe at the end of pre-processing.
    Will do most processes such as splitting reviews, removing Google's 
    "Translated by Google" texts and other functions. 

    Typical usage example:
        c = Cleaner(df)
        c.separateEmptyReview()
        c.remove_translated()
        c.remove_punctuation()
        c.lemmatize()
        c.expand_contracts()
        c.tokenizer()
        c.get_df()
    """

    def __init__(self, DataFrame):
        self.df = DataFrame

    def __enter__(self):
        return self

    def __exit__(self, exc_type, exc_value, tb):
        return True

    def separateEmptyReview(self):
        """Separates Empty and Non-Empty Reviews

        This function will move reviews with empty review_text to another dataframe
        that can be retrieved by get_empty_df() function.
        The non-empty review will remind in original dataframe object and 
        retrieved using get_df() function.
        """
        self.empty_df = self.df[self.df.review_text == ""].copy()
        self.df = self.df[self.df.review_text != ""].copy()

    def remove_translated(self):
        """Remove Google's untranslated review_text

        Since Google Reviews are automatically translated by Google. We can retrieve
        the english review however the review_text will have both translated and non-translated.
        This function will process and clean up translated reviews.
        """
        def __function(input_text):
            input_text = input_text.replace("(Translated by Google)", "")
            return input_text[:input_text.find("(Original)")].strip()
        self.__apply_to_review(__function)

    def remove_punctuation(self):
        """Remove punctuations

        Punctuation includes !"#$%&\()*+,-./:;<=>?@[\\]^_{|}~
        """
        def __function(input_text):
            return input_text.translate(str.maketrans('', '', PUNCTUATIONS))
        self.__apply_to_review(__function)

    def lemmatize(self):
        """ Convert words in review_text to its root-form

        Processing review_text by running it through a Part-of-Speech tagging and 
        grouping together inflected forms of a word.
        """
        def __function(input_text):
            textBlob = TextBlob(input_text)
            tag_dict = {"J": 'a', "N": 'n', "V": 'v', "R": 'r'}
            words_and_tags = [(w, tag_dict.get(pos[0], 'n'))
                              for w, pos in textBlob.tags]
            lemmatized_list = [wd.lemmatize(tag) for wd, tag in words_and_tags]
            return ' '.join(lemmatized_list)
        self.__apply_to_review(__function)

    def expand_contracts(self):
        """ Expanding contractions found in review_text.

        Uses a fixed algorithm for speed.
        """
        def __function(input_text):
            translation = {
                r"won\'t": " will not", r"can\'t": " can not",
                r"n\'t": " not", r"\'re": " are",
                r"\'s": " is", r"\'d": " would",
                r"\'ll": " will", r"\'t": " not",
                r"\'ve": " have", r"\'m": " am"
            }
            for key, value in translation.items():
                input_text = re.sub(key, value, input_text)
            return input_text
        self.__apply_to_review(__function)

    def tokenizer(self):
        """Split review_text into an list.

        Using textBlob to process and split the review_text into each word in a list
        """
        def __function(input_text):
            textBlob = TextBlob(input_text)
            return textBlob.lower().words
        self.df.review_text = self.df.apply(
            lambda x: __function(x.review_text), axis=1)

    def __apply_to_review(self, func):
        self.df.review_text = np.vectorize(func)(self.df.review_text)

    def get_empty_df(self):
        """Returns a dataframe of reviews with a empty review_text.

        This will return a dataframe containing reviews without a review_text.
        This dataframe is generated after separateEmptyReview() function is used.
        None will be return if the function was not used.

        Returns:
        a Dataframe containing reviews without review_text.
        """
        try:
            self.empty_df
        except NameError:
            return None
        else:
            return self.empty_df.copy()

    def get_df(self):
        """Returns a dataframe of reviews at the current state.

        This will return a dataframe containing reviews and affected by
        whichever function has been ran before.

        Returns:
        a Dataframe containing reviews.
        """
        return self.df.copy()
