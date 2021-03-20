from selenium.webdriver.common.keys import Keys
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions as EC
from selenium.common.exceptions import NoSuchElementException
from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from bs4 import BeautifulSoup
from datetime import datetime
import time
import Logger
import traceback
from GoogleReview import GoogleReview

MAX_WAIT = 10
MAX_RETRY = 5
DEFAULT_PULL = 100


class GoogleReviewScraper:
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

    def setURL(self, url, name=None):
        name = url if name == None else name
        self.logger.info('Attempting to load the Google Maps for '+name)
        self.driver.get(url)
        self.logger.info('Sucessfully navigated to '+name+' Google Maps page.')

    def sort_by_date(self):
        wait = WebDriverWait(self.driver, MAX_WAIT)
        clicked = False
        tries = 0
        self.logger.info('Attempting to sort Reviews by Dates')
        while not clicked and tries < MAX_RETRY:
            try:
                menu_bt = wait.until(EC.element_to_be_clickable(
                    (By.XPATH, '//button[@data-value=\'Sort\']')))
                menu_bt.click()
                clicked = True
                time.sleep(3)
            except Exception as e:
                tries += 1
                self.logger.warn(
                    'Failed to click "Show More" button, will retry! (Attempt: '+str(tries)+'/'+str(MAX_RETRY)+")")
            if tries == MAX_RETRY:
                self.logger.warn(
                    'Failed to sort Reviews by Dates, this scrape should be terminated, returning False ')
                return False
        self.logger.info('Reviews has been sorted by Dates.')
        recent_rating_bt = self.driver.find_elements_by_xpath(
            '//li[@role=\'menuitemradio\']')[1]
        recent_rating_bt.click()
        time.sleep(5)
        return True

    def get_reviews(self, offset=0):
        self.logger.info('Getting Review from Review '+str(offset)+' onwards.')
        self.__scroll()
        time.sleep(4)
        self.__expand_reviews()
        response = BeautifulSoup(self.driver.page_source, 'html.parser')
        rblock = response.find_all('div', class_='section-review-content')
        reviews = []
        for index, review in enumerate(rblock):
            if index >= offset:
                reviews.append(GoogleReview(review))
        return reviews

    def __expand_reviews(self):
        links = self.driver.find_elements_by_xpath(
            '//button[@class=\'section-expand-review blue-link\']')
        for l in links:
            l.click()
        time.sleep(2)

    def __scroll(self):
        scrollable_div = self.driver.find_element_by_css_selector(
            'div.section-layout.section-scrollbox.scrollable-y.scrollable-show')
        self.driver.execute_script(
            'arguments[0].scrollTop = arguments[0].scrollHeight', scrollable_div)

    def __get_driver(self, debug=False):
        options = Options()
        if not self.debug:
            options.add_argument("--headless")
        else:
            options.add_argument("--window-size=1366,768")
        options.add_argument('--no-sandbox')
        options.add_argument("--disable-notifications")
        options.add_argument("--lang=en-GB")
        input_driver = webdriver.Chrome(chrome_options=options)
        return input_driver
