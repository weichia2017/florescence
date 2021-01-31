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
        """Fetches all rows from Stores table.
        
        Retrieves all rows from the Stores table that will return return
        the Store's ID, Name, GoogleReview URL, and TripAdvisor URL.
        The URLs may consist of empty Strings, indicating there's no URL.
        
        Args:
            return_as_dataframe: Optional, default True; if return_as_dataframe is True, 
                The returned object will be in a pandas.DataFrame format else a List.
    
        Returns:
            Returns a nested list of stores and each row will consist of the following format
            ['store_id', 'store_name', 'googlereviews_url','tripadvisor_url']
            If return_as_dataframe  is set to True, a pandas.DataFrame object
            is returned with the columns and indexes set accordingly.
        """
        query = 'SELECT * FROM `stores`'
        output = self.__executeSelectQuery(query)
        if not return_as_dataframe:
            return output
        else:
            df = pandas.DataFrame(output, columns = STORE_HEADER)
            df.set_index('store_id', inplace=True)
            return df
    
    def getStore(self, store_id, return_as_dataframe = True):
        """Fetches a single row from Stores table.
        
        Retrieves a row from the Stores table from a provided store's ID.
        This will return return the Store's ID, Name, GoogleReview URL, and TripAdvisor URL.
        The URLs may consist of empty Strings, indicating there's no URL.
        
        Args:
            store_id: Required; the store's id to be retrieved from the database.
            return_as_dataframe: Optional, default True; if return_as_dataframe is True, 
                The returned object will be in a pandas.DataFrame format else a List.
                
        Returns:
            Returns a nested list of stores and each row will consist of the following format
            ['store_id', 'store_name', 'googlereviews_url','tripadvisor_url']
            If return_as_dataframe  is set to True, a pandas.DataFrame object
            is returned with the columns and indexes set accordingly.
        """
        if store_id == None:
            return None
        query = 'SELECT * FROM `stores` WHERE store_id = %s'
        args = (store_id,)
        output = self.__executeSelectQuery(query, args)
        if not return_as_dataframe:
            return output
        else:
            df = pandas.DataFrame(output, columns = STORE_HEADER)
            df.set_index('store_id', inplace=True)
            return df
        
    def writeRawGoogleReview(self, review):
        """Write a row into Google Reviews table.
        
        Writes a single row into Google Reviews table.
        Take note that review_id is a unique key therefore no duplicates are allowed.
        
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
        Take note that review_id is a unique key therefore no duplicates are allowed.
        
        Args:
            review: Tripadvisor Class.
            
        Returns:
            A boolean rather if the insertion was successful or not.
        """
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

    def getAllRawGoogleReviews(self, return_as_dataframe = True):
        """Retrieve all Google Reviews from the Database from All Stores
        
        Retrieves all rows from Google Reviews table.
        
        Args:
            return_as_dataframe: Optional, default True; if return_as_dataframe is True, 
                The returned object will be in a pandas.DataFrame format else a List.
                
        Returns:
            Returns a nested list of reviews from Google
            If return_as_dataframe is set to True, a pandas.DataFrame object
            is returned with the columns and indexes set accordingly.
        """
        query = 'SELECT * FROM `google_reviews`'
        output = self.__executeSelectQuery(query)
        if not return_as_dataframe:
            return output
        else:
            df = pandas.DataFrame(output, columns = GOOGLE_REVIEW_HEADER)
            df.set_index('review_id', inplace=True)
            return df
    
    def getRawGoogleReviews(self, store_id, return_as_dataframe = True):
        """Retrieve all Google Reviews from the Database from specified store.
        
        Retrieves all rows from Google Reviews table for a specific store given by store_id args.
        
        Args:
            store_id: the store id, getStores() to find the code ID.
            return_as_dataframe: Optional, default True; if return_as_dataframe is True, 
                The returned object will be in a pandas.DataFrame format else a List.
                
        Returns:
            Returns a nested list of reviews from Google
            If return_as_dataframe is set to True, a pandas.DataFrame object
            is returned with the columns and indexes set accordingly.
        """
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

    def getAllRawTripAdvisorReviews(self, return_as_dataframe = True):
        """Retrieve all Tripadvisor Reviews from the Database from All Stores
        
        Retrieves all rows from Tripadvisor Reviews table.
        
        Args:
            return_as_dataframe: Optional, default True; if return_as_dataframe is True, 
                The returned object will be in a pandas.DataFrame format else a List.
                
        Returns:
            Returns a nested list of reviews from Tripadvisor
            If return_as_dataframe is set to True, a pandas.DataFrame object
            is returned with the columns and indexes set accordingly.
        """
        query = 'SELECT * FROM `tripadvisor_reviews`'
        output = self.__executeSelectQuery(query)
        if not return_as_dataframe:
            return output
        else:
            df = pandas.DataFrame(output, columns = TRIP_ADVISOR_HEADER)
            df.set_index('review_id', inplace=True)
            return df
    
    def getRawTripAdvisorReviews(self, store_id, return_as_dataframe = True):
        """Retrieve all Tripadvisor from the Database from specified store.
        
        Retrieves all rows from Tripadvisor table for a specific store given by store_id args.
        
        Args:
            store_id: the store id, getStores() to find the code ID.
            return_as_dataframe: Optional, default True; if return_as_dataframe is True, 
                The returned object will be in a pandas.DataFrame format else a List.
                
        Returns:
            Returns a nested list of reviews from Tripadvisor
            If return_as_dataframe is set to True, a pandas.DataFrame object
            is returned with the columns and indexes set accordingly.
        """
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

    def getAllRawReviews(self, show_all = False, return_as_dataframe = True):
        """Retrieve all reviews from the Database from specified store.
        
        Retrieve all reviews from both Tripadvisor and Google. Columns will be handled according to args provided.
        A 'source' column was added to designate the origins of the review.
        
        Args:
            show_all: Optional, default False; all columns are returned by default however since
                reviews from both site are different, there will be np.NaN included.
                if show_all is set to True, only columns that is used by the sources
                columns will be returned.
            return_as_dataframe: Optional, default True; if return_as_dataframe is True, 
                The returned object will be in a pandas.DataFrame format else a List.
                
        Returns:
            Returns a nested list of reviews from all sources
            If return_as_dataframe is set to True, a pandas.DataFrame object
            is returned with the columns and indexes set accordingly, else a list is returned
        """
        gdf = this.getAllRawGoogleReviews(True)
        gdf['source'] = "Google"
        tdf = this.getAllRawTripAdvisorReviews(True)
        tdf['source'] = "Tripadvisor"
        df = pandas.concat([gdf,tdf])
        if not show_all:
            df = df[SHARED_HEADER]
        if return_as_dataframe:        
            return df
        else:
            return df.reset_index().values.tolist()

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