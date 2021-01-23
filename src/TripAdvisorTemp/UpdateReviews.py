import time
import datetime

from random import randrange

from selenium import webdriver
from selenium.webdriver.common.keys import Keys

from openpyxl import load_workbook


# Reading and Writing
workbookToReadWriteName = 'TripAdvisorScrapeURLs.xlsx'
wbReadWrite = load_workbook(workbookToReadWriteName)
readingWorkingFile = wbReadWrite["Sheet1"]

# Writing via Appending
workbookToWriteName = 'TripAdvisorRawData.xlsx'
wbWrite = load_workbook(workbookToWriteName)
wbWritePage = wbWrite.active

# Step1: Open Chromdriver
driver = webdriver.Chrome("./chromedriver")

# rowToWriteOn                = 0
shopNameColumn              = 2
noOfReviewsColumn           = 4
noOfNewReviewsColumn        = 5
dateUpdatedColumn           = 6 
urlColumn                   = 7

dateUpdated = datetime.datetime.now().strftime("%d/%m/%Y")

# Step2: Read the shop URL row by row and see if there is any update in reviews. If there is,
#        append the reviews in TripAdvisorRawData.xlsx
# for row in readingWorkingFile.iter_rows():
for rowno, rowval in enumerate(readingWorkingFile.iter_rows(min_row=2, max_row=readingWorkingFile.max_row), start=2):    
   
    urlOfShop   = readingWorkingFile.cell(row=rowno,column=urlColumn).value
    noOfReviews = 0 if (readingWorkingFile.cell(row=rowno,column=noOfReviewsColumn).value == None) else readingWorkingFile.cell(row=rowno,column=noOfReviewsColumn).value
    nameOfShop  = readingWorkingFile.cell(row=rowno,column=shopNameColumn).value
    
    #Step4: If the URL begins with tripadvisor and is not the header
    if(urlOfShop != "URL" and "https://www.tripadvisor.com.sg/Restaurant_Review-" in urlOfShop ):
        driver.get(urlOfShop)
        
        time.sleep(1)
        
        # Step5: Get the Total number of English Reviews
        newNoOfReviews = int(driver.find_element_by_xpath("//label[@for='filters_detail_language_filterLang_en']").text.split("(")[1][:-1])
        noOfReviewsToPull = newNoOfReviews - noOfReviews
        newReviews = noOfReviewsToPull
        
        while(noOfReviewsToPull > 0):
            
            time.sleep(2)
            # Step 6: Click to expand all reviews, basically clicking on the '...More' link
            # driver.find_element_by_xpath("//span[@class='taLnk ulBlueLinks']").click()
            moreLink =driver.find_element_by_xpath("//span[@class='taLnk ulBlueLinks']")
            driver.execute_script("arguments[0].click();", moreLink)

            time.sleep(2)

            # Step 4: Get the list of reviews which would typically be 10 per page
            listOfReviews = driver.find_elements_by_xpath("//div[@class='review-container']")
            
            # Step5: For each review append to TripAdvisorRawData.xlsx
            for review in listOfReviews:
                
                if(noOfReviewsToPull == 0):
                    break
                
                # Username
                username = review.find_element_by_xpath(".//div[@class='info_text pointer_cursor']/div").text
                
                # Number of Reviews
                isPencilIconThere = review.find_elements_by_xpath(".//span[@class='ui_icon pencil-paper']/following-sibling::span")
                noOfReviews = int((review.find_element_by_xpath(".//span[@class='badgeText']").text.split(" ")[0]).replace(",","")) if (isPencilIconThere == []) else int((isPencilIconThere[0].text).replace(",",""))
                
                # Rating
                overallRating = int(review.find_element_by_xpath(".//span[contains(@class, 'ui_bubble_rating bubble_')]").get_attribute("class").split("rating bubble_")[1][:-1])

                # Date
                date = review.find_element_by_xpath(".//span[contains(@class, 'ratingDate')]").get_attribute("title")
                dateReviewed = (datetime.datetime.strptime(date, '%d %B %Y')).strftime("%d/%m/%Y")    

                # Title
                title = review.find_element_by_xpath(".//span[@class='noQuotes']").text
                
                # Review
                textReview = review.find_element_by_xpath(".//p[@class='partial_entry']").text

                # Value Rating
                isThereExtraReviews = review.find_elements_by_xpath(".//li[@class='recommend-answer']")
                
                valueRating      = "-"
                atmosphereRating = "-"
                serviceRating    = "-"
                foodRating       = "-"
                
                if(isThereExtraReviews != []):
                    
                    for extraReview in isThereExtraReviews:
                        # Value Rating
                        if(extraReview.text == "Value"):
                            valueRating = int(extraReview.find_element_by_xpath(".//preceding-sibling::div").get_attribute("class").split("rating bubble_")[1][:-1])
                            
                        # Atmosphere Rating
                        elif(extraReview.text == "Atmosphere"):
                            atmosphereRating = int(extraReview.find_element_by_xpath(".//preceding-sibling::div").get_attribute("class").split("rating bubble_")[1][:-1])
                            
                        # Service Rating
                        elif(extraReview.text == "Service"):
                            serviceRating = int(extraReview.find_element_by_xpath(".//preceding-sibling::div").get_attribute("class").split("rating bubble_")[1][:-1])
                        
                        # Food Rating
                        else:
                            foodRating = int(extraReview.find_element_by_xpath(".//preceding-sibling::div").get_attribute("class").split("rating bubble_")[1][:-1])
                
                reviewRow = [nameOfShop, 
                            dateUpdated,
                            username,
                            noOfReviews, 
                            overallRating, 
                            dateReviewed,
                            title,
                            textReview, 
                            valueRating,
                            atmosphereRating,
                            serviceRating,
                            foodRating]
                
                wbWritePage.append(reviewRow)
                noOfReviewsToPull -=1 
                
            if(noOfReviewsToPull != 0): 
                sleepTime = randrange(5,15)
                print("Sleeping for", sleepTime,"seconds before next page")
                time.sleep(sleepTime)
                driver.find_element_by_xpath('//a[@class="nav next ui_button primary"]').click()

        readingWorkingFile.cell(row=rowno, column=noOfReviewsColumn).value = newNoOfReviews
        readingWorkingFile.cell(row=rowno, column=noOfNewReviewsColumn).value = newReviews
        readingWorkingFile.cell(row=rowno, column=dateUpdatedColumn).value = dateUpdated  
        if(newReviews != 0):
            print("Updated %d new review(s) for %s" % (newReviews, nameOfShop))
        else:
            print("No new reviews for %s" % (nameOfShop))
    
        sleepTime = randrange(5,15)
        print("Sleeping for", sleepTime,"seconds before next shop\n")
        time.sleep(sleepTime)
    
    
    #Step: If the URL does not begin with tripadvisor 
    else:
        readingWorkingFile.cell(row=rowno, column=noOfReviewsColumn).value    = "-"
        readingWorkingFile.cell(row=rowno, column=noOfNewReviewsColumn).value = "-"
        readingWorkingFile.cell(row=rowno, column=dateUpdatedColumn).value    = "-"
        
# Step 8: Save the file or nothing will be saved
wbReadWrite.save(workbookToReadWriteName)
wbWrite.save(workbookToWriteName)

# Step 9: Close the chromedriver
driver.quit()  