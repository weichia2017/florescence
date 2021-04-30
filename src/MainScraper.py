import time
import datetime
import Logger
import traceback
from random import randrange
from GoogleReviewScraper import GoogleReviewScraper
from TripAdvisorScraper import TripAdvisorScraper
from DatabaseAccess import DataAccess

class Scraper:
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

    def scrapeStore(self, store_id):
        self.logger.info("Starting Scrape for Store ID:" + str(store_id))
        with DataAccess() as dao:
            stores = dao.getStore(store_id, True)
            stores.dropna(inplace=True)
            stores.reset_index(inplace=True)
            for index, row in stores.iterrows():
                if (row['googlereviews_url'] != ""):
                    self.logger.info("Executing Google Scraping Function")
                    self.__scrape_google(row, dao)
                if (row['tripadvisors_url'] != ""):
                    self.logger.info("Executing TripAdvisor Scraping Function")
                    self.__scrape_tripadvisor(row, dao)
        self.logger.info("Scrape completed")

    def __scrape_google(self, row, dao):
        running = True
        number_to_scrape = 1000
        store_id = row['store_id']
        store_name = row['store_name']
        store_url = row['googlereviews_url']
        with GoogleReviewScraper(debug=self.debug_mode) as scraper:
            scraper.setURL(url=store_url, name=store_name)
            noError = scraper.sort_by_date()
            if noError == True:
                n = 0
                while n < number_to_scrape:
                    reviews = scraper.get_reviews(n)
                    for r in reviews:
                        r.setStoreID(store_id)
                        status = dao.writeRawReviews(r, 1)
                        if not status:
                            running = False
                            break
                    n += len(reviews)
                    if (len(reviews) == 0) or not running:
                        break

    def __scrape_tripadvisor(self, row, dao):
        dateUpdated = datetime.datetime.now().strftime("%Y:%m:%d")
        store_id = row['store_id']
        store_name = row['store_name']
        store_url = row['tripadvisors_url']
        with TripAdvisorScraper(debug=self.debug_mode) as scraper:
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
                    if not dao.writeRawReviews(review_object, 2):
                        self.logger.info(
                            "Reviews scrape are all up to date. No (more) new Reviews found.")
                        doesNotExistInDB = False
                        break
                if(doesNotExistInDB):
                    nextButtonExist = scraper.clickNextPageButton()
                    if (nextButtonExist):
                        sleepTime = randrange(5, 15)
                        self.logger.info(
                            "Sleeping for " + str(sleepTime) + " seconds before next page")
                        time.sleep(sleepTime)
