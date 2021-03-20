import Logger
import traceback
from DatabaseAccess import DataAccess
import numpy as np
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
        with DataAccess() as dao:
            raw_df = dao.getRawReviews_UnProcessed()
            if raw_df.empty:
                self.logger.info("No reviews to process, halting processing")
                return None
        self.logger.info("Processing "+str(len(raw_df))+" Raw Reviews")
        df = __remove_translated(raw_df).reset_index()

        def vaderSent(text):
            analyzer = SentimentIntensityAnalyzer()
            vs = analyzer.polarity_scores(text)
            return f"{vs['neg']},{vs['neu']},{vs['pos']},{vs['compound']}"
        self.logger.info("Applying Vader Sentiment Analysis on Reviews")
        df['vader_score'] = np.vectorize(vaderSent)(df.review_text)
        df[['negative', 'neutral', 'positive', 'compound']
           ] = df.vader_score.str.split(",", expand=True).astype(float)
        self.logger.info("Vader Sentiment Analysis Completed")
        rows = df[['review_id', 'source_id', 'negative',
                   'neutral', 'positive', 'compound']]
        self.logger.info("Inserting to Database")
        for row in rows.itertuples():
            try:
                dao.writeSentiments(row)
            except:
                self.logger.error(
                    "An error occurred for (" + ','.join(row)+")")
            finally:
                self.logger.info("Completed Database Insertions")

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
