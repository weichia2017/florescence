from datetime import datetime
import Logger
import traceback 
from DatabaseAccess import DataAccess
import numpy as np
import pandas as pd
import spacy
from vaderSentiment.vaderSentiment import SentimentIntensityAnalyzer

class Processor:
    def __init__(self, debug_mode=False):
        self.debug_mode = debug_mode
        self.logger = Logger.get_logger(__name__)

    def __enter__(self):
        return self

    def __exit__(self, exc_type, exc_value, tb):
        if exc_type is not None:
            traceback.print_exception(exc_type, exc_value, tb)
        self.logger.handlers.clear()
        return True

    def processSentiments(self):
        self.logger.info("Running Sentiment Processing")
        with DataAccess() as dao:
            raw_df = dao.getRawReviews_UnProcessed_Sentiments()
            if raw_df.empty:
                self.logger.info("No new review(s) to generate Sentiments, Function Halted")
                return None
        self.logger.info(str(len(raw_df))+" reviews pending for Sentiment Analysis")
        df = self.__remove_translated(raw_df).reset_index()

        def vaderSent(text):
            analyzer = SentimentIntensityAnalyzer()
            vs = analyzer.polarity_scores(text)
            return f"{vs['neg']},{vs['neu']},{vs['pos']},{vs['compound']}"
        self.logger.info("Performing Sentiment Analysis")
        df['vader_score'] = np.vectorize(vaderSent)(df.review_text)
        df[['negative', 'neutral', 'positive', 'compound']
           ] = df.vader_score.str.split(",", expand=True).astype(float)
        self.logger.info("Sentiment Analysis Completed")
        rows = df[['review_id', 'source_id', 'negative',
                   'neutral', 'positive', 'compound']]
        self.logger.info("Inserting Sentiments to Database")
        for row in rows.itertuples():
            with DataAccess() as dao:
                try:
                    dao.writeSentiments(row, datetime.now())
                except:
                    self.logger.error("An error occurred for (" + ','.join(row)+")")
        self.logger.info("Sentiments Processing completed")
        
    def processAdjNounPairs(self):
        self.logger.info("running Adjective-Noun Processing")
        with DataAccess() as dao:
            raw_df = dao.getRawReviews_UnProcessed_AdjNounPairs()
            if raw_df.empty:
                self.logger.info("No new review(s) to generate Adjective-Noun Pairs, halting processing")
                return None
        self.logger.info(str(len(raw_df))+" reviews pending for Adjective-Noun Analysis")
        df = self.__remove_translated(raw_df).reset_index()
        self.logger.info("Loading Resources and Setting Parameters")
        nlp = spacy.load('en_core_web_sm')
        lemmatizer = nlp.get_pipe("lemmatizer")
        def spcy(row):
            all_pairs = pd.DataFrame(columns=['review_id','source_id','noun', 'adj'])
            doc = nlp(row.review_text)
            for token in doc:
                if (token.pos_ == 'ADJ' and 
                    token.dep_ == "amod" and 
                    token.head != None and 
                    token.head.pos_ in ['PROPN', 'NOUN'] and 
                    token.text != "-"):
                        new_row = {'review_id': row.review_id, 
                                   'source_id': row.source_id, 
                                   'noun': token.head.lemma_.lower(), 
                                   'adj': token.lemma_.lower()}
                        all_pairs = all_pairs.append(new_row, ignore_index=True)
            return all_pairs
        self.logger.info("Performing Adjective-Noun Pairs Analysis")
        all_pairs = df.apply(spcy, axis=1)
        all_pairs = pd.concat(all_pairs.to_list())
        all_pairs['source_id'] = all_pairs['source_id'].astype('int')
        self.logger.info("Adjective-Noun Pairs Analysis Completed")
        self.logger.info("Inserting to Database")
        for row in all_pairs.itertuples():
            with DataAccess() as dao:
                try:
                    dao.writeAdjNounPairs(row, datetime.now())
                except:
                    self.logger.error("An error occurred for (" + ','.join(row)+")")
        self.logger.info("Adjective-Noun Pairs Processing completed")
        
        
    def __remove_translated(self, df):
        def __function(input_text):
            input_text = input_text.replace("(Translated by Google)", "")
            original_index = input_text.find("(Original)")
            if original_index == -1:
                return input_text.strip()
            return input_text[:original_index].strip()
        df.review_text = np.vectorize(__function)(df.review_text)
        return df

    def __apply_to_review(self, func):
        self.df.review_text = np.vectorize(func)(self.df.review_text)
from datetime import datetime
import Logger
import traceback 
from DatabaseAccess import DataAccess
import numpy as np
import pandas as pd
import spacy
from vaderSentiment.vaderSentiment import SentimentIntensityAnalyzer

class Processor:
    def __init__(self, debug_mode=False):
        self.debug_mode = debug_mode
        self.logger = Logger.get_logger(__name__)

    def __enter__(self):
        return self

    def __exit__(self, exc_type, exc_value, tb):
        if exc_type is not None:
            traceback.print_exception(exc_type, exc_value, tb)
        self.logger.handlers.clear()
        return True

    def processSentiments(self):
        self.logger.info("Running Sentiment Processing")
        with DataAccess() as dao:
            raw_df = dao.getRawReviews_UnProcessed_Sentiments()
            if raw_df.empty:
                self.logger.info("No new review(s) to generate Sentiments, Function Halted")
                return None
        self.logger.info(str(len(raw_df))+" reviews pending for Sentiment Analysis")
        df = self.__remove_translated(raw_df).reset_index()

        def vaderSent(text):
            analyzer = SentimentIntensityAnalyzer()
            vs = analyzer.polarity_scores(text)
            return f"{vs['neg']},{vs['neu']},{vs['pos']},{vs['compound']}"
        self.logger.info("Performing Sentiment Analysis")
        df['vader_score'] = np.vectorize(vaderSent)(df.review_text)
        df[['negative', 'neutral', 'positive', 'compound']
           ] = df.vader_score.str.split(",", expand=True).astype(float)
        self.logger.info("Sentiment Analysis Completed")
        rows = df[['review_id', 'source_id', 'negative',
                   'neutral', 'positive', 'compound']]
        self.logger.info("Inserting Sentiments to Database")
        for row in rows.itertuples():
            with DataAccess() as dao:
                try:
                    dao.writeSentiments(row, datetime.now())
                except:
                    self.logger.error("An error occurred for (" + ','.join(row)+")")
        self.logger.info("Sentiments Processing completed")
        
    def processAdjNounPairs(self):
        self.logger.info("running Adjective-Noun Processing")
        with DataAccess() as dao:
            raw_df = dao.getRawReviews_UnProcessed_AdjNounPairs()
            if raw_df.empty:
                self.logger.info("No new review(s) to generate Adjective-Noun Pairs, halting processing")
                return None
        self.logger.info(str(len(raw_df))+" reviews pending for Adjective-Noun Analysis")
        df = self.__remove_translated(raw_df).reset_index()
        self.logger.info("Loading Resources and Setting Parameters")
        num_of_noun = 10
        num_of_adj_each = 3
        nlp = spacy.load('en_core_web_sm')
        lemmatizer = nlp.get_pipe("lemmatizer")
        def spcy(row):
            all_pairs = pd.DataFrame(columns=['review_id','source_id','noun', 'adj'])
            doc = nlp(row.review_text)
            for token in doc:
                if (token.pos_ == 'ADJ' and 
                    token.dep_ == "amod" and 
                    token.head != None and 
                    token.head.pos_ in ['PROPN', 'NOUN'] and 
                    token.text != "-"):
                        new_row = {'review_id': row.review_id, 
                                   'source_id': row.source_id, 
                                   'noun': token.head.lemma_.lower(), 
                                   'adj': token.lemma_.lower()}
                        all_pairs = all_pairs.append(new_row, ignore_index=True)
            return all_pairs
        self.logger.info("Performing Adjective-Noun Pairs Analysis")
        all_pairs = df.apply(spcy, axis=1)
        all_pairs = pd.concat(all_pairs.to_list())
        all_pairs['source_id'] = all_pairs['source_id'].astype('int')
        self.logger.info("Adjective-Noun Pairs Analysis Completed")
        self.logger.info("Inserting to Database")
        for row in all_pairs.itertuples():
            with DataAccess() as dao:
                try:
                    dao.writeAdjNounPairs(row, datetime.now())
                except:
                    self.logger.error("An error occurred for (" + ','.join(row)+")")
        self.logger.info("Adjective-Noun Pairs Processing completed")
        
        
    def __remove_translated(self, df):
        def __function(input_text):
            input_text = input_text.replace("(Translated by Google)", "")
            original_index = input_text.find("(Original)")
            if original_index == -1:
                return input_text.strip()
            return input_text[:original_index].strip()
        df.review_text = np.vectorize(__function)(df.review_text)
        return df

    def __apply_to_review(self, func):
        self.df.review_text = np.vectorize(func)(self.df.review_text)
