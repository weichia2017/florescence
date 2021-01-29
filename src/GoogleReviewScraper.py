from selenium.webdriver.common.keys import Keys
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions as EC
from selenium.common.exceptions import NoSuchElementException
from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from bs4 import BeautifulSoup
from datetime import datetime
import time, re, logging, traceback
from GoogleReview import GoogleReview

MAX_WAIT = 10
MAX_RETRY = 5
DEFAULT_PULL = 100

class Scraper:
    """Scraper module for Scraping Data from Google Maps Reviews.

    This scraper module will scrape from Google Maps and collect information based on a provided
    URL specified by setURL() method. Because the Selenium Driver is running --headless mode, 
    a Logger will record down all activity into 'logger.log' for tracking.
    
    Typical usage example:
        scraperObject = Scraper()
        scraper.setURL(url="URL", name="Name")
        scraper.sort_by_date()
        offset = 0
        LIMIT = 100
        while offset <= LIMIT:
            reviews = scraper.get_reviews(offset)
            offset += reviews.length
    """

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

    def setURL(self, url, name = None):
        """Sets the Selenium Driver to the provided URL

        Sets the Selenium Driver to URL and load the page.

        Args:
            url: Required; a valid Google Maps URL.
            name: Optional; Used for making logging clean.
                If name is not set, URL will be logged.
        """
        name = url if name == None else name
        self.logger.info('Attempting to load the Google Maps for '+name)
        self.driver.get(url)
        self.logger.info('Sucessfully navigated to '+name+' Google Maps page.')

    def sort_by_date(self):
        """Sort the reviews by newest first

        Have the Selenium Driver select 'Sort' button and then select 'Newest'
        This method will attempt to sort by newest with the maximum of MAX_RETRY times.
        If the method fails to sort, it will return a False boolean and the scrape should be terminated
        as the reviews will be unsorted and only shows the more relevant reviews.

        Returns:
            A boolean if the Reviews has been sorted by Newest.
        """ 
        wait = WebDriverWait(self.driver, MAX_WAIT)
        clicked = False
        tries = 0
        self.logger.info('Attempting to sort Reviews by Dates')
        while not clicked and tries < MAX_RETRY:
            try:
                menu_bt = wait.until(EC.element_to_be_clickable((By.XPATH, '//button[@data-value=\'Sort\']')))
                menu_bt.click()
                clicked = True
                time.sleep(3)
            except Exception as e:
                tries += 1
                self.logger.warn('Failed to click "Show More" button, will retry! (Attempt: '+str(tries)+'/'+str(MAX_RETRY)+")")
            if tries == MAX_RETRY:
                self.logger.warn('Failed to sort Reviews by Dates, this scrape should be terminated, returning False ')
                return False
        self.logger.info('Reviews has been sorted by Dates.')
        recent_rating_bt = self.driver.find_elements_by_xpath('//li[@role=\'menuitemradio\']')[1]
        recent_rating_bt.click()
        time.sleep(5)
        return True

    def get_reviews(self, offset = 0):
        """Retrieve the reviews from the Google Maps Reviews

        This method will scroll down and have the webpage fetch for reviews then retrieve 
        the reviews between offset to the maxmium number of reviews visible on the webpage.
        By using the returned list, you can get the length and add to the offset before 
        running this method again to get the next batch of reviews.

        Args:
            offset: The starting point of the Scrape for Reviews
        
        Returns:
            A List of GoogleReview() objects. It will return reviews between
            offset to the maximum number of reviews visible on the page.
            
        """ 
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
        links = self.driver.find_elements_by_xpath('//button[@class=\'section-expand-review blue-link\']')
        for l in links:
            l.click()
        time.sleep(2)

    def __scroll(self):
        scrollable_div = self.driver.find_element_by_css_selector('div.section-layout.section-scrollbox.scrollable-y.scrollable-show')
        self.driver.execute_script('arguments[0].scrollTop = arguments[0].scrollHeight', scrollable_div)

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