from dotenv import load_dotenv
import mysql.connector
from mysql.connector import errorcode
import os, warnings
import time, re, logging, traceback
import pandas

from GoogleReview import GoogleReview

GOOGLE_REVIEW_HEADER = ['id_review', 'caption', 'relative_date','retrieval_date', 'rating', 
                        'username', 'n_review_user', 'n_photo_user', 'url_user', 'store_id']
STORE_HEADER = ['store_id', 'store_name', 'googlereviews_url','tripadvisor_url']

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

    def getStores(self, dataframeReturnType = False):
        """Fetches all rows from Stores table.

        Retrieves all rows from the Stores table that will return return
        the Store's ID, Name, GoogleReview URL, and TripAdvisor URL.
        The URLs may consist of empty Strings, indicating there's no URL.

        Args:
            dataframeReturnType: Optional; if dataframeReturnType is True, 
                The returned object will be in a pandas.DataFrame format.

        Returns:
            Returns a nested list of stores and each row will consist of the following format
            ['store_id', 'store_name', 'googlereviews_url','tripadvisor_url']
            If dataframeReturnType  is set to True, a pandas.DataFrame object
            is returned with the columns and indexes set accordingly.
        """
        query = 'SELECT * FROM `stores`'
        output = self.__executeSelectQuery(query)
        if not dataframeReturnType:
            return output
        else:
            df = pandas.DataFrame(output, columns = STORE_HEADER)
            df.set_index('store_id', inplace=True)
            return df
    
    def getStore(self, store_id, dataframeReturnType = False):
        """Fetches a single row from Stores table.

        Retrieves a row from the Stores table from a provided store's ID.
        This will return return the Store's ID, Name, GoogleReview URL, and TripAdvisor URL.
        The URLs may consist of empty Strings, indicating there's no URL.

        Args:
            store_id: Required; the store's id to be retrieved from the database.
            dataframeReturnType: Optional; if dataframeReturnType is True, 
                The returned object will be in a pandas.DataFrame format.

        Returns:
            Returns a nested list of stores and each row will consist of the following format
            ['store_id', 'store_name', 'googlereviews_url','tripadvisor_url']
            If dataframeReturnType  is set to True, a pandas.DataFrame object
            is returned with the columns and indexes set accordingly.
        """
        if store_id == None:
            return None
        query = 'SELECT * FROM `stores` WHERE store_id = %s'
        args = (store_id,)
        output = self.__executeSelectQuery(query, args)
        if not dataframeReturnType:
            return output
        else:
            df = pandas.DataFrame(output, columns = STORE_HEADER)
            df.set_index('store_id', inplace=True)
            return df
        
    def writeRawGoogleReview(self, review):
        """Write a row into Google Reviews table.

        Writes a single row into Google Reviews table.
        Take note that id_review is a unique key therefore no duplicates are allowed.

        Args:
            review: GoogleReview Class.

        Returns:
            A boolean rather if the insertion was successful or not.
        """
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
    
    def writeRawTripAdvisorReview(self, review):
        """Write a row into Tripadvisor table.

        Writes a single row into Tripadvisor Reviews table.
        Take note that id_review is a unique key therefore no duplicates are allowed.

        Args:
            review: Tripadvisor Class.

        Returns:
            A boolean rather if the insertion was successful or not.
        """
        query = '''
            INSERT INTO `tripadvisor_reviews` 
            (`review_id`, `store_id`, `review_text`, `review_date`, `rating`, `username`, 
            `n_review_user`, `retrieval_date`, `review_title`, `valueRating`, `atmosphereRating`, 
            `serviceRating`, `foodRating`) 
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
                review.valueRating,
                review.atmosphereRating,
                review.serviceRating,
                review.foodRating)
        return self.__executeInsertQuery(query, args)

    def getAllRawGoogleReviews(self, dataframeReturnType = False):
        """Retrieve all Google Reviews from the Database from All Stores

        Retrieves all rows from Google Reviews table.

        Args:
            dataframeReturnType: Optional; if dataframeReturnType is True, 
                The returned object will be in a pandas.DataFrame format.

        Returns:
            Returns a nested list of stores and each row will consist of the following format
            ['store_id', 'store_name', 'googlereviews_url','tripadvisor_url']
            If dataframeReturnType  is set to True, a pandas.DataFrame object
            is returned with the columns and indexes set accordingly.
        """
        query = 'SELECT * FROM `google_reviews`'
        output = self.__executeSelectQuery(query)
        if not dataframeReturnType:
            return output
        else:
            df = pandas.DataFrame(output, columns = GOOGLE_REVIEW_HEADER)
            df.set_index('id_review', inplace=True)
            return df
    
    def getRawGoogleReviews(self, store_id, dataframeReturnType = False):
        """Retrieve all Google Reviews from the Database from specified store.

        Retrieves all rows from Google Reviews table for a specific store given by store_id args.

        Args:
            store_id: the store id, getStores() to find the code ID.
            dataframeReturnType: Optional; if dataframeReturnType is True, 
                The returned object will be in a pandas.DataFrame format.

        Returns:
            Returns a nested list of stores and each row will consist of the following format
            ['store_id', 'store_name', 'googlereviews_url','tripadvisor_url']
            If dataframeReturnType  is set to True, a pandas.DataFrame object
            is returned with the columns and indexes set accordingly.
        """
        if store_id == None:
            return None
        query = 'SELECT * FROM `google_reviews` WHERE store_id = %s'
        args = (store_id,)
        output = self.__executeSelectQuery(query, args)
        if not dataframeReturnType:
            return output
        else:
            df = pandas.DataFrame(output, columns = GOOGLE_REVIEW_HEADER)
            df.set_index('id_review', inplace=True)
            return df

    def __executeInsertQuery(self, query, args):
        status = True
        try:
            cursor = self.connector.cursor()
            cursor.execute(query, args)
            self.connector.commit()
        except mysql.connector.Error as err:
            status = False
            self.logger.warn(err)
        finally:
            cursor.close()
        return status
    
    def __executeSelectQuery(self, query, args=None):
        results = None
        try:
            cursor = self.connector.cursor()
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
                database=os.environ.get("DB_BASE")
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