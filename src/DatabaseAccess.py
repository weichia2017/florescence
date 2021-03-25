from dotenv import load_dotenv
import mysql.connector
from mysql.connector import errorcode
import os
import warnings
import Logger
import pandas
import Logger
import traceback


class DataAccess:
    def __init__(self):
        load_dotenv('.env')
        self.connector = self.__get_connector()
        self.logger = Logger.get_logger(__name__)

    def __enter__(self):
        return self

    def __exit__(self, exc_type, exc_value, tb):
        if exc_type is not None:
            traceback.print_exception(exc_type, exc_value, tb)
        self.connector.close()
        self.logger.handlers.clear()
        return True

    # Store Information Functions

    def getStores(self, return_as_dataframe=True):
        query = 'SELECT * FROM `stores`'
        output = self.__executeSelectQuery(query)
        if not return_as_dataframe:
            return output
        else:
            df = pandas.DataFrame(output)
            df.set_index('store_id', inplace=True)
            return df

    def getStore(self, store_id, return_as_dataframe=True):
        if store_id == None:
            return None
        query = 'SELECT * FROM `stores` WHERE store_id = %s'
        args = (store_id,)
        output = self.__executeSelectQuery(query, args)
        if not return_as_dataframe:
            return output
        else:
            df = pandas.DataFrame(output)
            df.set_index('store_id', inplace=True)
            return df

    # Sentiment Functions

    def writeSentiments(self, row, datetime):
        query = '''
            INSERT INTO `sentiment_scores` (`review_id`, `source_id`, `negative`, `neutral`, `positive`, `compound`, processed_date) 
            VALUES (%s, %s, %s, %s, %s, %s, %s)
            '''
        args = (row.review_id, row.source_id, row.negative,
                row.neutral, row.positive, row.compound, datetime)
        return self.__executeInsertQuery(query, args)

    def getAllSentiments(self, return_as_dataframe=True):
        query = '''
                SELECT CONCAT(rr.review_id, '-', rr.source_id) as review_id, rr.store_id, 
                rr.review_text, rr.review_date, ss.compound as "compound_score"
                FROM raw_reviews rr JOIN sentiment_scores ss 
                ON rr.review_id = ss.review_id AND rr.source_id = ss.source_id
            '''
        output = self.__executeSelectQuery(query)
        if not return_as_dataframe:
            return output
        else:
            df = pandas.DataFrame(output)
            return df

    def getSentiments(self, store_id, return_as_dataframe=True):
        if store_id == None:
            return None
        query = '''
                SELECT CONCAT(rr.review_id, '-', rr.source_id) as review_id, 
                rr.review_text, rr.review_date, ss.compound as "compound_score"
                FROM raw_reviews rr JOIN sentiment_scores ss 
                ON rr.review_id = ss.review_id AND rr.source_id = ss.source_id WHERE rr.store_id = %s
            '''
        args = (store_id,)
        output = self.__executeSelectQuery(query, args)
        if not return_as_dataframe:
            return output
        else:
            df = pandas.DataFrame(output)
            return df

    def getRawReviews_UnProcessed_Sentiments(self, return_as_dataframe=True):
        query = '''
            SELECT * FROM raw_reviews rr WHERE rr.review_text != "" 
            AND NOT EXISTS (SELECT 1 FROM sentiment_scores ss WHERE rr.review_id = ss.review_id AND rr.source_id = ss.source_id)
        '''
        output = self.__executeSelectQuery(query)
        if len(output) == 0:
            return pandas.DataFrame()
        if not return_as_dataframe:
            return output
        else:
            df = pandas.DataFrame(output)
            df.set_index('review_id', inplace=True)
            return df

    # Adjective Noun Pairs Functions

    def writeAdjNounPairs(self, row, datetime):
        query = '''
            INSERT INTO `adj_noun_pairs` (`pair_id`, `review_id`, `source_id`, `noun`, `adj`, `processed_date`) 
            VALUES (NULL, %s, %s, %s, %s, %s)
        '''
        args = (row.review_id, row.source_id, row.noun, row.adj, datetime)
        return self.__executeInsertQuery(query, args)

    def getAdjNounPairs(self, store_id, return_as_dataframe=True):
        query = '''
        SELECT CONCAT(anp.review_id,'-', anp.source_id) as review_id, anp.noun, anp.adj FROM raw_reviews rr JOIN adj_noun_pairs anp
            ON rr.review_id = anp.review_id AND rr.source_id = anp.source_id WHERE rr.store_id = %s
        '''
        args = (store_id,)
        output = self.__executeSelectQuery(query, args)
        if not return_as_dataframe:
            return output
        else:
            df = pandas.DataFrame(output)
            return df

    def getAdjNounPairsByIds(self, ids, return_as_dataframe=True):
        query = '''
            SELECT CONCAT(anp.review_id,'-', anp.source_id) as review_id, anp.noun, anp.adj FROM raw_reviews rr JOIN adj_noun_pairs anp
            ON rr.review_id = anp.review_id AND rr.source_id = anp.source_id WHERE CONCAT(rr.review_id,'-',rr.source_id) IN 
        ''' + str(tuple(ids))
        output = self.__executeSelectQuery(query)
        if not return_as_dataframe:
            return output
        else:
            df = pandas.DataFrame(output)
            return df   

    def getRawReviews_UnProcessed_AdjNounPairs(self, return_as_dataframe=True):
        query = '''
            SELECT * FROM raw_reviews rr WHERE rr.review_text != "" 
            AND NOT EXISTS (SELECT 1 FROM adj_noun_pairs ss WHERE rr.review_id = ss.review_id AND rr.source_id = ss.source_id)
        '''
        output = self.__executeSelectQuery(query)
        if len(output) == 0:
            return pandas.DataFrame()
        if not return_as_dataframe:
            return output
        else:
            df = pandas.DataFrame(output)
            df.set_index('review_id', inplace=True)
            return df

    # Review Functions

    def writeRawReviews(self, review, source_id):
        query = '''
            INSERT INTO `raw_reviews` 
            (`review_id`, `source_id`, `store_id`, `review_text`, `review_date`, `retrieval_date`) 
            VALUES 
            (%s, %s, %s, %s, %s, %s)
            '''
        args = (review.review_id,
                source_id,
                review.store_id,
                review.review_text,
                review.review_date,
                review.retrieval_date)
        return self.__executeInsertQuery(query, args)

    def __executeInsertQuery(self, query, args):
        cursor = self.connector.cursor()
        status = True
        try:
            cursor.execute(query, args)
            self.connector.commit()
        except mysql.connector.Error as err:
            status = False
            self.logger.warn(err)
        finally:
            cursor.close()
        return status

    def __executeSelectQuery(self, query, args=None):
        cursor = self.connector.cursor(dictionary=True)
        results = None
        try:
            cursor.execute(query, args)
            results = cursor.fetchall()
        except mysql.connector.Error as err:
            self.logger.warn(err)
        finally:
            cursor.close()
        return results

    def __get_connector(self):
        try:
            mydb = mysql.connector.connect(
                host=os.environ.get("DB_HOST"),
                user=os.environ.get("DB_USER"),
                passwd=os.environ.get("DB_PASS"),
                database=os.environ.get("DB_BASE"),
                collation='utf8mb4_unicode_ci'      # For the emojis
            )
            return mydb
        except mysql.connector.Error as err:
            if err.errno == errorcode.ER_ACCESS_DENIED_ERROR:
                self.logger.warn(
                    "Something is wrong with your user name or password")
            elif err.errno == errorcode.ER_BAD_DB_ERROR:
                self.logger.warn("Database does not exist")
            else:
                self.logger.warn(err)
        else:
            mydb.close()
            return None
