import datetime 

class TripAdvisorReview:    
    def __init__(self, review,store_id):        
        self.value_rating      = None
        self.atmosphere_rating = None
        self.service_rating    = None
        self.food_rating       = None
        
        # Review id
        self.review_id = review.get_attribute("data-reviewid")
        
        # Store id
        self.store_id = store_id
        
        # Date Updated
        self.retrieval_date = None
        
        # Store name
        self.store_name = None
        
        # Username
        self.username = review.find_element_by_xpath(".//div[@class='info_text pointer_cursor']/div").text
        
        # Number Of Reviews made by user
        isPencilIconThere  = review.find_elements_by_xpath(".//span[@class='ui_icon pencil-paper']/following-sibling::span")
        self.n_review_user = int((review.find_element_by_xpath(".//span[@class='badgeText']").text.split(" ")[0]).replace(",","")) if (isPencilIconThere == []) else int((isPencilIconThere[0].text).replace(",",""))
    
        # Overall rating for shop
        self.rating = int(review.find_element_by_xpath(".//span[contains(@class, 'ui_bubble_rating bubble_')]").get_attribute("class").split("rating bubble_")[1][:-1])
        
        # Date the review was made by user
        date = review.find_element_by_xpath(".//span[contains(@class, 'ratingDate')]").get_attribute("title")
        # print(str(date))
        self.review_date = (datetime.datetime.strptime(date, '%d %B %Y')).strftime("%Y:%m:%d")   
        
        # Title of Review
        self.review_title = review.find_element_by_xpath(".//span[@class='noQuotes']").text 
    
        # The Text Review
        self.review_text = review.find_element_by_xpath(".//p[@class='partial_entry']").text.replace("\n", " ")
        
        # Extra Review namely, 1.Value, 2.Atmosphere, 3.Service, 4.Food
        isThereExtraReviews =  review.find_elements_by_xpath(".//li[@class='recommend-answer']")
    
        if(isThereExtraReviews != []):           
            for extraReview in isThereExtraReviews:
                # Value Rating
                if(extraReview.text == "Value"):
                    self.value_rating = int(extraReview.find_element_by_xpath(".//preceding-sibling::div").get_attribute("class").split("rating bubble_")[1][:-1])
                    
                # Atmosphere Rating
                elif(extraReview.text == "Atmosphere"):
                    self.atmosphere_rating = int(extraReview.find_element_by_xpath(".//preceding-sibling::div").get_attribute("class").split("rating bubble_")[1][:-1])
                    
                # Service Rating
                elif(extraReview.text == "Service"):
                    self.service_rating = int(extraReview.find_element_by_xpath(".//preceding-sibling::div").get_attribute("class").split("rating bubble_")[1][:-1])
                
                # Food Rating
                else:
                    self.food_rating = int(extraReview.find_element_by_xpath(".//preceding-sibling::div").get_attribute("class").split("rating bubble_")[1][:-1])

        
    def __enter__(self):
        return self

    def __exit__(self, exc_type, exc_value, tb):
        return True