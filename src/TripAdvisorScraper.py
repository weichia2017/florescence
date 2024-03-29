from TripAdvisorReview import TripAdvisorReview
from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import time
import traceback
import Logger
from random import randrange

MAX_WAIT = 10
MAX_RETRY = 5


class TripAdvisorScraper:

    def __init__(self, debug=False):
        self.debug = debug
        self.driver = self.__get_driver()
        self.logger = Logger.get_logger(__name__)

    def __enter__(self):
        return self

    def __exit__(self, exc_type, exc_value, tb):
        if exc_type is not None:
            traceback.print_exception(exc_type, exc_value, tb)
        self.logger.handlers.clear()
        self.driver.close()
        self.driver.quit()
        return True

    def setURL(self, name, url):
        self.logger.info(
            'Browser opening an visiting the shop ' + name)
        self.driver.get(url)
        self.logger.info('Loaded ' + name)
        time.sleep(1)

    def showMoreButton(self):
        wait = WebDriverWait(self.driver, MAX_WAIT)
        clicked = False
        tries = 0
        while not clicked and tries < MAX_RETRY:
            try:
                show_more_bt = wait.until(EC.element_to_be_clickable(
                    (By.XPATH, "//span[@class='taLnk ulBlueLinks']")))
                show_more_bt.click()
                clicked = True
                time.sleep(3)
            except Exception:
                tries += 1
                self.logger.warn(
                    'Failed to click show more button, attempt: ' + str(tries))
            if tries == MAX_RETRY:
                return -1
        return 0

    def getReviews(self, store_id):
        time.sleep(2)
        list_of_review_objects = []

        self.showMoreButton()

        listOfReviews = self.driver.find_elements_by_xpath(
            "//div[@class='review-container']")

        for review in listOfReviews:
            with TripAdvisorReview(review, store_id) as review_object:
                list_of_review_objects.append(review_object)

        return list_of_review_objects

    def clickNextPageButton(self):
        wait = WebDriverWait(self.driver, MAX_WAIT)
        clicked = False
        tries = 0
        while not clicked and tries < MAX_RETRY:
            try:
                next_page_btn = wait.until(EC.element_to_be_clickable(
                    (By.XPATH, '//a[@class="nav next ui_button primary"]')))
                next_page_btn.click()
                clicked = True
                time.sleep(3)

            except Exception as e:
                tries += 1
                self.logger.warn(
                    'Failed to click next page button, attempt: ' + str(tries))

            if tries == MAX_RETRY:
                return False
        return True

    def __get_driver(self, debug=False):
        options = Options()
        if not self.debug:
            options.add_argument("--headless")
        else:
            options.add_argument("--window-size=1366,768")
        options.add_argument("--disable-notifications")
        options.add_argument("--lang=en-GB")
        input_driver = webdriver.Chrome(chrome_options=options)
        return input_driver
