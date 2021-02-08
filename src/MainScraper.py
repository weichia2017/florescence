import time
import datetime
from random import randrange
from GoogleReviewScraper import GoogleReviewScraper
from TripAdvisorScraper import TripAdvisorScraper
from DatabaseAccess import DataAccess


class MainScraper:

    def __init__(self, debug_mode=False):
        self.debug_mode = debug_mode
        self.logger = self.__get_logger()

    def ScrapeStore(self, store_id):
        with DataAccess() as dao:
            stores = dao.getStore(store_id, True)
            stores.dropna(inplace=True)
            stores.reset_index(inplace=True)
            for index, row in stores.iterrows():
                if (row['googlereviews_url'] != ""):
                    self.__scrape_google(row, dao)
                if (row['tripadvisor_url'] != ""):
                    self.__scrape_tripadvisor(row, dao)

    def __scrape_google(self, row, dao):
        store_id = row['store_id']
        store_name = row['store_name']
        store_url = row['googlereviews_url']
        with Scraper(debug=debug_mode) as scraper:
            scraper.setURL(url=store_url, name=store_name)
            noError = scraper.sort_by_date()
            if noError == True:
                n = 0
                while n < number_to_scrape:
                    reviews = scraper.get_reviews(n)
                    for r in reviews:
                        r.setStoreID(store_id)
                        status = dao.writeRawGoogleReview(r)
                        if not status:
                            running = False
                            break
                    n += len(reviews)
                    if (len(reviews) == 0) or not running:
                        break

    def __scrape_tripadvisor(self, row, dao):
        store_id = row['store_id']
        store_name = row['store_name']
        store_url = row['tripadvisor_url']
        with TripAdvisorScraper(debug=debug_mode) as scraper:
            doesNotExistInDB = True
            scraper.setURL(url=store_url, name=store_name)
            self.logger.info("Looking for new review(s) for " + store_name)
            nextButtonExist = True
            while(nextButtonExist and doesNotExistInDB):
                list_of_review_objects = []
                list_of_review_objects = scraper.getReviews(store_id)

                for review_object in list_of_review_objects:
                    review_object.store_name = store_name
                    review_object.retrieval_date = dateUpdated
                    if not dao.writeRawTripAdvisorReview(review_object):
                        self.logger.info(
                            "Reviews scrape are all up to date. No (more) new Reviews found.")
                        doesNotExistInDB = False
                        break
                if(doesNotExistInDB):
                    nextButtonExist = scraper.clickNextPageButton()
                    if (nextButtonExist):
                        sleepTime = randrange(5, 15)
                        self.logger.info(
                            "Sleeping for " + sleepTime + " seconds before next page")
                        time.sleep(sleepTime)

    def __get_logger(self):
        logger = logging.getLogger('logger')
        logger.setLevel(logging.DEBUG)
        logger.propagate = False
        fh = logging.FileHandler('logger.log')
        fh.setLevel(logging.DEBUG)
        formatter = logging.Formatter(
            '%(asctime)s - %(module)s (%(funcName)s) - %(levelname)s - %(message)s')
        fh.setFormatter(formatter)
        logger.addHandler(fh)
        return logger
