import datetime 

class Review:
    #Change all from "-" to None when using DB
    #DONT KILL ME FOR CHANGING THIS BACK YET WEI 
    valueRating      = "-"
    atmosphereRating = "-"
    serviceRating    = "-"
    foodRating       = "-"
    
    def __init__(self, review):
        self.review = review
        
    def __enter__(self):
        return self

    def __exit__(self, exc_type, exc_value, tb):
        return True
    
    # Username
    def __getUsername(self):
        return self.review.find_element_by_xpath(".//div[@class='info_text pointer_cursor']/div").text
    
    # Number Of Reviews made by user
    def __getNoOfReviews(self):
        isPencilIconThere = self.review.find_elements_by_xpath(".//span[@class='ui_icon pencil-paper']/following-sibling::span")
        return int((self.review.find_element_by_xpath(".//span[@class='badgeText']").text.split(" ")[0]).replace(",","")) if (isPencilIconThere == []) else int((isPencilIconThere[0].text).replace(",",""))
    
    # Overall rating for shop
    def __getOverallRating(self):
        return int(self.review.find_element_by_xpath(".//span[contains(@class, 'ui_bubble_rating bubble_')]").get_attribute("class").split("rating bubble_")[1][:-1])

    # Date the review was made by user
    def __getDateReviewed(self): 
        date = self.review.find_element_by_xpath(".//span[contains(@class, 'ratingDate')]").get_attribute("title")
        # print(str(date))
        return (datetime.datetime.strptime(date, '%d %B %Y')).strftime("%d/%m/%Y")    
    
    # Title of Review
    def __getTitle(self):
        return self.review.find_element_by_xpath(".//span[@class='noQuotes']").text
    
    # The Text Review
    def __getTextReview(self):
        return  self.review.find_element_by_xpath(".//p[@class='partial_entry']").text
    
    # Extra Review namely, 1.Value, 2.Atmosphere, 3.Service, 4.Food
    def __getExtraReviews(self):
        isThereExtraReviews = self.review.find_elements_by_xpath(".//li[@class='recommend-answer']")
    
        if(isThereExtraReviews != []):           
            for extraReview in isThereExtraReviews:
                # Value Rating
                if(extraReview.text == "Value"):
                    self.valueRating = int(extraReview.find_element_by_xpath(".//preceding-sibling::div").get_attribute("class").split("rating bubble_")[1][:-1])
                    
                # Atmosphere Rating
                elif(extraReview.text == "Atmosphere"):
                    self.atmosphereRating = int(extraReview.find_element_by_xpath(".//preceding-sibling::div").get_attribute("class").split("rating bubble_")[1][:-1])
                    
                # Service Rating
                elif(extraReview.text == "Service"):
                    self.serviceRating = int(extraReview.find_element_by_xpath(".//preceding-sibling::div").get_attribute("class").split("rating bubble_")[1][:-1])
                
                # Food Rating
                else:
                    self.foodRating = int(extraReview.find_element_by_xpath(".//preceding-sibling::div").get_attribute("class").split("rating bubble_")[1][:-1])
    
    def get_Review(self,dateUpdated,nameOfShop):
        
        username               = self.__getUsername()
        noOfReviewsMadeByUser  = self.__getNoOfReviews()
        overallRating          = self.__getOverallRating()
        dateReviewed           = self.__getDateReviewed()
        titleOfReview          = self.__getTitle()
        textOfReview           = self.__getTextReview()
        self.__getExtraReviews()
       
        #Discuss with WEI why i need to have nameOfShop and dateUpdated here
        #DONT KILL ME FOR CHANGING THIS BACK YET WEI     
        return [nameOfShop,
                dateUpdated,             
                username, 
                noOfReviewsMadeByUser,
                overallRating,
                dateReviewed,
                titleOfReview,
                textOfReview,
                self.valueRating,
                self.atmosphereRating,
                self.serviceRating,
                self.foodRating]