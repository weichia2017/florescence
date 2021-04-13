from dotenv import load_dotenv
import mysql.connector
from mysql.connector import errorcode
import os
import warnings
import Logger
import pandas
import traceback

class DataAccess:
    def __init__(self):
        load_dotenv('.env')
        self.logger = Logger.get_logger(__name__)
        self.connector = self.__get_connector()

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
        query = '''
            SELECT
                s.store_id,
                s.store_name,
                s.road_id,
                s.googlereviews_url,
                s.tripadvisors_url,
                AVG(ss.compound) as "average_compound",
                COUNT(ss.compound) as "num_of_reviews",
                (2+COUNT(ss.compound)*AVG(ss.compound))/(3+2+COUNT(ss.compound)) as "beta_score"
            FROM
                stores s
            LEFT JOIN raw_reviews rr ON
                s.store_id = rr.store_id
            LEFT JOIN sentiment_scores ss ON
                ss.review_id = rr.review_id AND ss.source_id = rr.source_id
            GROUP BY
                s.store_id
            ORDER BY
                (3+COUNT(ss.compound)*AVG(ss.compound))/(2+3+COUNT(ss.compound)) DESC
        '''
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
        query = '''
            SELECT
                s.store_id,
                s.store_name,
                s.road_id,
                s.googlereviews_url,
                s.tripadvisors_url,
                AVG(ss.compound) as "average_compound",
                COUNT(ss.compound) as "num_of_reviews",
                (2+COUNT(ss.compound)*AVG(ss.compound))/(3+2+COUNT(ss.compound)) as "beta_score"
            FROM
                stores s
            LEFT JOIN raw_reviews rr ON
                s.store_id = rr.store_id
            LEFT JOIN sentiment_scores ss ON
                ss.review_id = rr.review_id AND ss.source_id = rr.source_id
            WHERE
                s.store_id = %s
            GROUP BY
                s.store_id
            ORDER BY
                (3+COUNT(ss.compound)*AVG(ss.compound))/(2+3+COUNT(ss.compound)) DESC
        '''
        args = (store_id,)
        output = self.__executeSelectQuery(query, args)
        if not return_as_dataframe:
            return output
        else:
            df = pandas.DataFrame(output)
            df.set_index('store_id', inplace=True)
            return df

    def getStoreByRoad(self, road_id, return_as_dataframe=True):
        if road_id == None:
            return None
        query = '''
            SELECT
                s.store_id,
                s.store_name,
                s.road_id,
                s.googlereviews_url,
                s.tripadvisors_url,
                AVG(ss.compound) as "average_compound",
                COUNT(ss.compound) as "num_of_reviews",
                (2+COUNT(ss.compound)*AVG(ss.compound))/(3+2+COUNT(ss.compound)) as "beta_score"
            FROM
                stores s
            LEFT JOIN raw_reviews rr ON
                s.store_id = rr.store_id
            LEFT JOIN sentiment_scores ss ON
                ss.review_id = rr.review_id AND ss.source_id = rr.source_id
            WHERE
            	s.road_id = %s
            GROUP BY
                s.store_id
            ORDER BY
                (3+COUNT(ss.compound)*AVG(ss.compound))/(2+3+COUNT(ss.compound)) DESC
        '''
        args = (road_id,)
        output = self.__executeSelectQuery(query, args)
        if not return_as_dataframe:
            return output
        else:
            df = pandas.DataFrame(output)
            df.set_index('store_id', inplace=True)
            return df

    def getRoads(self, return_as_dataframe=True):
        query = '''
            SELECT * FROM roads
            '''
        output = self.__executeSelectQuery(query)
        if not return_as_dataframe:
            return output
        else:
            df = pandas.DataFrame(output)
            df.set_index('road_id', inplace=True)
            return df

    def getRoad(self, road_id, return_as_dataframe=True):
        query = '''
            SELECT * FROM roads WHERE road_id = %s
            '''
        args = (road_id,)
        output = self.__executeSelectQuery(query, args)
        if not return_as_dataframe:
            return output
        else:
            df = pandas.DataFrame(output)
            df.set_index('road_id', inplace=True)
            return df

    # Sentiment Functions

    def writeSentiments(self, row, datetime):
        query = '''
            INSERT INTO `sentiment_scores` (`review_id`, `source_id`, `negative`, `neutral`, `positive`, `compound`, processed_date)
            VALUES (%s, %s, %s, %s, %s, %s, %s)
            '''
        args = (row.review_id, row.source_id, row.negative,
                row.neutral, row.positive, row.compound, datetime)
        return self.__executeModificationQuery(query, args)

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

    def getSentimentsByStore(self, store_id, return_as_dataframe=True):
        if store_id == None:
            return None
        query = '''
            SELECT CONCAT(rr.review_id, '-', rr.source_id) as review_id, rr.review_text, rr.review_date, ss.compound as "compound_score"
            FROM raw_reviews rr
            JOIN sentiment_scores ss ON rr.review_id = ss.review_id AND rr.source_id = ss.source_id WHERE rr.store_id = %s
            '''
        args = (store_id,)
        output = self.__executeSelectQuery(query, args)
        if not return_as_dataframe:
            return output
        else:
            df = pandas.DataFrame(output)
            return df

    def getSentimentsByRoad(self, road_id, return_as_dataframe=True):
        if road_id == None:
            return None
        query = '''
            SELECT s.store_id, CONCAT(rr.review_id, '-', rr.source_id) as review_id, rr.review_text, rr.review_date, ss.compound as "compound_score"
            FROM raw_reviews rr
            JOIN sentiment_scores ss ON rr.review_id = ss.review_id AND rr.source_id = ss.source_id
            JOIN stores s ON rr.store_id = s.store_id
            WHERE s.road_id = %s
            '''
        args = (road_id,)
        output = self.__executeSelectQuery(query, args)
        if not return_as_dataframe:
            return output
        else:
            df = pandas.DataFrame(output)
            return df

    # Adjective Noun Pairs Functions

    def writeAdjNounPairs(self, row, datetime):
        query = '''
            INSERT INTO `adj_noun_pairs` (`pair_id`, `review_id`, `source_id`, `noun`, `adj`, `processed_date`)
            VALUES (NULL, %s, %s, %s, %s, %s)
        '''
        args = (row['review_id'], row['source_id'], row['noun'], row['adj'], datetime)
        return self.__executeModificationQuery(query, args)

    def getAdjNounPairsByStore(self, store_id, return_as_dataframe=True):
        query = '''
            SELECT CONCAT(anp.review_id,'-', anp.source_id) as review_id, anp.noun, anp.adj
            FROM raw_reviews rr
            JOIN adj_noun_pairs anp ON rr.review_id = anp.review_id AND rr.source_id = anp.source_id
            WHERE rr.store_id = %s
        '''
        args = (store_id,)
        output = self.__executeSelectQuery(query, args)
        if not return_as_dataframe:
            return output
        else:
            df = pandas.DataFrame(output)
            return df

    def getAdjNounPairsByRoad(self, road_id, return_as_dataframe=True):
        query = '''
            SELECT CONCAT(anp.review_id,'-', anp.source_id) as review_id, anp.noun, anp.adj
            FROM raw_reviews rr
            JOIN adj_noun_pairs anp ON rr.review_id = anp.review_id AND rr.source_id = anp.source_id
            JOIN stores s ON rr.store_id = s.store_id
            WHERE s.road_id = %s
        '''
        args = (road_id,)
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

    ## Pre-Processing

    def getRawReviews_UnProcessed_Sentiments(self):
        query = '''
            SELECT * FROM raw_reviews rr WHERE rr.review_text != ""
            AND NOT EXISTS (SELECT 1 FROM sentiment_scores ss WHERE rr.review_id = ss.review_id AND rr.source_id = ss.source_id)
        '''
        output = self.__executeSelectQuery(query)
        if len(output) == 0:
            return pandas.DataFrame()
        df = pandas.DataFrame(output)
        df.set_index('review_id', inplace=True)
        return df

    def getRawReviews_UnProcessed_AdjNounPairs(self):
        query = '''
            SELECT * FROM raw_reviews rr WHERE rr.review_text != ""
            AND NOT EXISTS (SELECT 1 FROM adj_noun_pairs ss WHERE rr.review_id = ss.review_id AND rr.source_id = ss.source_id)
            AND rr.retrieval_date >= (SELECT MAX(processed_date) FROM adj_noun_pairs)
        '''
        output = self.__executeSelectQuery(query)
        if len(output) == 0:
            return pandas.DataFrame()
        df = pandas.DataFrame(output)
        df.set_index('review_id', inplace=True)
        return df

    # Reviews

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
        return self.__executeModificationQuery(query, args)

    ## Users

    def createUser(self, email, name, hashed_password):
        query = '''
            INSERT INTO `users`
            (`user_id`, `email`, `name`, `password`, `active`, `admin`, `store_id`)
            VALUES
            (UUID_SHORT(), %s, %s, %s, True, False, NULL)
            '''
        args = (email, name, hashed_password)
        return self.__executeModificationQuery(query, args)

    def getUserByEmail(self, email):
        query = '''
            SELECT * FROM users WHERE email = %s
        '''
        args = (email,)
        return self.__executeSelectQuery(query, args)

    def getUserByUserId(self, user_id):
        query = '''
            SELECT * FROM users WHERE user_id = %s
        '''
        args = (user_id,)
        return self.__executeSelectQuery(query, args)

    def updateUserPassword(self, user_id, password):
        query = '''
            UPDATE `users` SET `password` = %s WHERE `users`.`user_id` = %s;
        '''
        args = (password, user_id)
        return self.__executeModificationQuery(query, args)

    def updateStoreId(self, user_id, store_id):
        query = '''
            UPDATE `users` SET `store_id` = %s WHERE `users`.`user_id` = %s;
        '''
        args = (store_id, user_id)
        return self.__executeModificationQuery(query, args)

    ## Utility

    def __executeModificationQuery(self, query, args):
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
