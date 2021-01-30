from selenium import webdriver
from selenium.webdriver.chrome.options import Options

from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

from Review import Review
import time, logging, traceback
from random import randrange

MAX_WAIT = 10
MAX_RETRY = 3

class Scraper:
    
    def __init__(self, debug=False):
        self.debug = debug
        self.driver = self.__get_driver()
        self.logger = self.__get_logger()

    def __enter__(self):
        return self

    def __exit__(self, exc_type, exc_value, tb):
        if exc_type is not None:
            traceback.print_exception(exc_type, exc_value, tb)
        self.driver.close()
        self.driver.quit()
        return True
    
    def setURL(self, name, url):
        self.logger.warn('Browser opening an visiting the shop ' + name + ' at ' + url)
        self.driver.get(url)
        self.logger.warn('Loaded ' + name)
        time.sleep(1)
    
    def showMoreButton(self):
        wait = WebDriverWait(self.driver, MAX_WAIT)
        clicked = False
        tries = 0
        while not clicked and tries < MAX_RETRY:
            try:
                menu_bt = wait.until(EC.element_to_be_clickable((By.XPATH, "//span[@class='taLnk ulBlueLinks']")))
                menu_bt.click()
                clicked = True
                time.sleep(3)
            except Exception:
                tries += 1
                self.logger.warn('Failed to click recent button, attempt: '+ str(tries))
            if tries == MAX_RETRY:
                return -1
        return 0
    
    
    def getReviews(self,store_id):
        time.sleep(2)
        list_of_review_objects = []
            
        self.showMoreButton()
       
        listOfReviews = self.driver.find_elements_by_xpath("//div[@class='review-container']")
            
        for review in listOfReviews:
            with Review(review,store_id) as review_object:
                list_of_review_objects.append(review_object)

        return list_of_review_objects
    
    def clickNextPageButton(self):
        wait = WebDriverWait(self.driver, MAX_WAIT)
        clicked = False
        tries = 0
        while not clicked and tries < MAX_RETRY:
            try:
                nextButton = wait.until(EC.element_to_be_clickable((By.XPATH, '//a[@class="nav next ui_button primary"]')))
                nextButton.click()
                clicked = True
                time.sleep(3)

            except Exception as e:
                tries += 1
                self.logger.warn('Failed to click recent button, attempt: '+ str(tries))
                
            if tries == MAX_RETRY:
                return False
            
        return True
    
    def __get_logger(self):
        logger = logging.getLogger('TripAdvisorScraper')
        logger.setLevel(logging.DEBUG)
        fh = logging.FileHandler('TripAdvisorScraper.log')
        fh.setLevel(logging.DEBUG)
        formatter = logging.Formatter('%(asctime)s - %(levelname)s - %(message)s')
        fh.setFormatter(formatter)
        logger.addHandler(fh)
        return logger

    def __get_driver(self, debug=False):
        options = Options()
        if not self.debug:
            options.add_argument("--headless")
        else:
            options.add_argument("--window-size=1366,768")
        options.add_argument("--disable-notifications")
        options.add_argument("--lang=en-GB")
        input_driver = webdriver.Chrome("./chromedriver", chrome_options=options)
        return input_driver