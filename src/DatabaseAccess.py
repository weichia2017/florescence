from dotenv import load_dotenv
import mysql.connector
import os, warnings
import time, re, logging, traceback
import pandas
GOOGLE_REVIEW_HEADER = ['id_review', 'caption', 'relative_date','retrieval_date', 'rating', 
                        'username', 'n_review_user', 'n_photo_user', 'url_user', 'store']
class PDO:    
    def __init__(self):
        load_dotenv('.env')
        self.connector = self.__get_connector()
        self.logger = self.__get_logger()

    def __enter__(self):
        return self

    def __exit__(self, exc_type, exc_value, tb):
        if exc_type is not None:
            traceback.print_exception(exc_type, exc_value, tb)
        self.connection.close()
        return True
    
    def writeRawGoogleReview(self, row):
        query = '''
            INSERT INTO googlereviews 
            (`id_review`, `caption`, `relative_date`, `retrieval_date`, `rating`, 
            `username`, `n_review_user`, `n_photo_user`, `url_user`, `store`) 
            VALUES 
            (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
            '''
        args = (row[0], row[1], row[2], row[3], row[4], row[5], row[6], row[7], row[8], row[9])
        return self.__executeInsertQuery(query, args)
    
    def getAllRawGooleReview(self, returnType = False):
        query = 'SELECT * FROM `googlereviews`'
        output = self.__executeSelectQuery(query)
        if not returnType:
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
            self.__log_warn(err)
        finally:
            cursor.close()
        return status
    
    def __executeSelectQuery(self, query):
        results = None
        try:
            cursor = self.connector.cursor()
            cursor.execute(query)
            results = cursor.fetchall()           
        except mysql.connector.Error as err:
            self.__log_warn(err)
        finally:
            cursor.close()
        return results
        
    def __get_connector(self):
        mydb = mysql.connector.connect(
            host=os.environ.get("DB_HOST"),
            user=os.environ.get("DB_USER"),
            passwd=os.environ.get("DB_PASS"),
            database=os.environ.get("DB_BASE")
        )
        return mydb
    
    def __get_logger(self):
        logger = logging.getLogger('logger')
        logger.setLevel(logging.DEBUG)
        fh = logging.FileHandler('logger.log')
        fh.setLevel(logging.DEBUG)
        formatter = logging.Formatter('%(asctime)s - %(module)s (%(funcName)s) - %(levelname)s - %(message)s')
        fh.setFormatter(formatter)
        logger.addHandler(fh)
        return logger
    
    def __log_info(self, info):
        self.logger.warn(info)
    
    def __log_warn(self, warn):
        self.logger.warn(warn)