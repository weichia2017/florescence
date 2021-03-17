from dotenv import load_dotenv
import mysql.connector
from mysql.connector import errorcode
import os, warnings
import time, re, logging, traceback
import pandas

GOOGLE_REVIEW_HEADER = ['review_id', 'store_id', 'review_text', 'review_date', 'rating', 'username', 
    'n_review_user', 'retrieval_date', 'n_photo_user', 'url_user', 'relative_date']
TRIP_ADVISOR_HEADER = ['review_id','store_id','review_text','review_date','rating','username',
   'n_review_user','retrieval_date','review_title','value_rating',
    'atmosphere_rating','service_rating','food_rating']
STORE_HEADER = ['store_id', 'store_name', 'googlereviews_url','tripadvisor_url']
SHARED_HEADER = ['store_id', 'review_text', 'review_date', 'rating', 'username',
       'n_review_user', 'retrieval_date', 'source']

class DataAccess:
    """DataAccess module for Florescence MySQL Database.
    
    This DataAccess object requires .env object that includes the following variable
        DB_HOST: Endpoint of the MySQL Database
        DB_USER: Username of a user account with sufficient read/write access
        DB_PASS: Password of the respective user account
        DB_BASE: Respective Schema/Database to be used.
    This module includes methods to retrieve and insert data to and fro the MySQL Database 
    and most methods includes a parameter that will format the output into a pandas.DataFrame.
    
    Typical usage example:
        dao = DataAccess()
        storeList = dao.getStores()
        storeDataFrame = dao.getStores(True)
    """
    def __init__(self):
        load_dotenv('.env')
        self.connector = self.__get_connector()
        self.logger = self.__get_logger()

    def __enter__(self):
        return self

    def __exit__(self, exc_type, exc_value, tb):
        if exc_type is not None:
            traceback.print_exception(exc_type, exc_value, tb)
        self.connector.close()
        return True

    def getStores(self, return_as_dataframe = True):
        query = 'SELECT * FROM `stores`'
        output = self.__executeSelectQuery(query)
        if not return_as_dataframe:
            return output
        else:
            df = pandas.DataFrame(output)
            df.set_index('store_id', inplace=True)
            return df
    
    def getStore(self, store_id, return_as_dataframe = True):
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

    #TODO: To be replaced with a universal write
    def writeGoogleReview(self, review):
        query = '''
            INSERT INTO `google_reviews` 
            (`review_id`, `store_id`, `review_text`, `review_date`, `rating`, `username`, 
            `n_review_user`, `retrieval_date`, `n_photo_user`, `url_user`, `relative_date`) 
            VALUES 
            (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
            '''
        args = (review.review_id,
                review.store_id,
                review.review_text, 
                review.getEstimatedDate(),  
                review.rating, 
                review.username, 
                review.n_review_user, 
                review.retrieval_date,
                review.n_photo_user, 
                review.user_url,
                review.relative_date)
        return self.__executeInsertQuery(query, args)

    #TODO: To be replaced with a universal write
    def writeTripAdvisorReview(self, review):
        query = '''
            INSERT INTO `tripadvisor_reviews` 
            (`review_id`, `store_id`, `review_text`, `review_date`, `rating`, `username`, 
            `n_review_user`, `retrieval_date`, `review_title`, `value_rating`, `atmosphere_rating`, 
            `service_rating`, `food_rating`) 
            VALUES 
            (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
            '''
        args = (review.review_id,
                review.store_id,
                review.review_text, 
                review.review_date,  
                review.rating, 
                review.username, 
                review.n_review_user, 
                review.retrieval_date,
                review.review_title, 
                review.value_rating,
                review.atmosphere_rating,
                review.service_rating,
                review.food_rating)
        return self.__executeInsertQuery(query, args)

    def writeSentiments(self, row):
        query = '''
            INSERT INTO `sentiment_scores` (`review_id`, `source_id`, `negative`, `neutral`, `positive`, `compound`) 
            VALUES (%s, %s, %s, %s, %s, %s)
            '''
        args = (row.review_id, row.source_id, row.negative, row.neutral, row.positive, row.compound)
        return self.__executeInsertQuery(query, args)
        
    #TODO: To be replaced with a universal write
    def getAllGoogleReviews(self, return_as_dataframe = True):
        query = 'SELECT * FROM `google_reviews`'
        output = self.__executeSelectQuery(query)
        if not return_as_dataframe:
            return output
        else:
            df = pandas.DataFrame(output, columns = GOOGLE_REVIEW_HEADER)
            df.set_index('review_id', inplace=True)
            return df
        
    #TODO: To be replaced with a universal write
    def getAllTripAdvisorReviews(self, return_as_dataframe = True):
        query = 'SELECT * FROM `tripadvisor_reviews`'
        output = self.__executeSelectQuery(query)
        if not return_as_dataframe:
            return output
        else:
            df = pandas.DataFrame(output, columns = TRIP_ADVISOR_HEADER)
            df.set_index('review_id', inplace=True)
            return df
    
    def getRawReviews(self, return_as_dataframe = True):
        query = 'SELECT * FROM raw_reviews WHERE review_text != ""'
        output = self.__executeSelectQuery(query)
        if not return_as_dataframe:
            return output
        else:
            df = pandas.DataFrame(output, columns = TRIP_ADVISOR_HEADER)
            df.set_index('review_id', inplace=True)
            return df
        
    def getRawReviews_UnProcessed(self, return_as_dataframe = True):
        query = 'SELECT * FROM raw_reviews rr WHERE rr.review_text != "" AND NOT EXISTS (SELECT 1 FROM sentiment_scores ss WHERE rr.review_id = ss.review_id AND rr.source_id = ss.source_id)'
        output = self.__executeSelectQuery(query)
        if len(output) == 0:
            return None
        if not return_as_dataframe:
            return output
        else:
            df = pandas.DataFrame(output)
            df.set_index('review_id', inplace=True)
            return df
        
    #TODO: To replace with universal raw getter
    def getAllReviews(self, show_all = False, return_as_dataframe = True):
        gdf = self.getAllGoogleReviews()
        gdf['source'] = "Google"
        tdf = self.getAllTripAdvisorReviews()
        tdf['source'] = "Tripadvisor"
        df = pandas.concat([gdf,tdf])
        if not show_all:
            df = df[SHARED_HEADER]
        if return_as_dataframe:        
            return df
        else:
            return df.reset_index().values.tolist()
        
    #TODO: To replace with universal raw getter
    def getStoreGoogleReviews(self, store_id, return_as_dataframe = True):
        if store_id == None:
            return None
        query = 'SELECT * FROM `google_reviews` WHERE store_id = %s'
        args = (store_id,)
        output = self.__executeSelectQuery(query, args)
        if not return_as_dataframe:
            return output
        else:
            df = pandas.DataFrame(output, columns = GOOGLE_REVIEW_HEADER)
            df.set_index('review_id', inplace=True)
            return df
    
    #TODO: To replace with universal raw getter
    def getStoreTripAdvisorReviews(self, store_id, return_as_dataframe = True):
        if store_id == None:
            return None
        query = 'SELECT * FROM `tripadvisor_reviews` WHERE store_id = %s'
        args = (store_id,)
        output = self.__executeSelectQuery(query, args)
        if not return_as_dataframe:
            return output
        else:
            df = pandas.DataFrame(output, columns = TRIP_ADVISOR_HEADER)
            df.set_index('review_id', inplace=True)
            return df

    #TODO: To replace with universal raw getter
    def getStoreReviews(self, store_id, show_all = False, return_as_dataframe = True):
        gdf = self.getStoreGoogleReviews(store_id)
        gdf['source'] = "Google"
        tdf = self.getStoreTripAdvisorReviews(store_id)
        tdf['source'] = "Tripadvisor"
        df = pandas.concat([gdf,tdf])
        if not show_all:
            df = df[SHARED_HEADER]
        if return_as_dataframe:        
            return df
        else:
            return df.reset_index().values.tolist()

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
                self.logger.warn("Something is wrong with your user name or password")
            elif err.errno == errorcode.ER_BAD_DB_ERROR:
                self.logger.warn("Database does not exist")
            else:
                self.logger.warn(err)
        else:
            mydb.close()
            return None

    def __get_logger(self):
        logger = logging.getLogger('logger')
        logger.setLevel(logging.DEBUG)
        logger.propagate = False
        fh = logging.FileHandler('logger.log')
        fh.setLevel(logging.DEBUG)
        formatter = logging.Formatter('%(asctime)s - %(module)s (%(funcName)s) - %(levelname)s - %(message)s')
        fh.setFormatter(formatter)
        logger.addHandler(fh)
        return logger