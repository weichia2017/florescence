from datetime import datetime
from datetime import timedelta
from dateutil.relativedelta import relativedelta


class GoogleReview:
    """GoogleReview contains information for individual reviews.

    This object is initialized by GoogleReviewScraper by passing in the review string
    from the Google Maps Review webpage.

    Attributes:
        review_id: The Unique Code for the review
        username: Username of the reviewer
        review_text: The contents and feedback from the reviewer
        rating: The rating given by the reviwer
        relative_date: There is no exact timestamp provided by Google. It uses a relative date.
            e.g. a week ago, 3 weeks ago, 1 month ago.
        n_review_user: Number of reviews the user has made before
        n_photos_user: Numbers of photo the user has made before
        user_url: URL to the profile of the reviewer
        retrieval_date: The date that this review for retrieved.
    """

    def __init__(self, review):
        self.review_id = review.find(
            'button', class_='section-review-action-menu')['data-review-id']
        self.username = review.find(
            'div', class_='section-review-title').find('span').text
        try:
            review_text = self.__filter_string(review.find(
                'span', class_='section-review-text').text)
        except Exception as e:
            review_text = None
        self.review_text = review_text
        self.rating = float(review.find(
            'span', class_='section-review-stars')['aria-label'].split(' ')[1])
        self.relative_date = review.find(
            'span', class_='section-review-publish-date').text
        try:
            n_reviews_photos = review.find(
                'div', class_='section-review-subtitle').find_all('span')[1].text
            metadata = n_reviews_photos.split('\xe3\x83\xbb')
            if len(metadata) == 3:
                n_photos = int(metadata[2].split(' ')[0].replace('.', ''))
            else:
                n_photos = 0
            idx = len(metadata)
            self.n_review_user = int(
                metadata[idx - 1].split(' ')[0].replace('.', ''))
            self.n_photo_user = n_photos
        except Exception as e:
            self.n_review_user = 0
            self.n_photo_user = 0
        self.user_url = review.find('a')['href']
        self.retrieval_date = datetime.now()

    def setStoreID(self, store_id):
        self.store_id = store_id

    def getEstimatedDate(self):
        split = self.relative_date.split()
        n = 1 if split[0][0] == 'a' else int(split[0])
        split[1] = split[1][:-1] if split[1][-1] == "s" else split[1]
        compare = {
            'hour': timedelta(hours=n),
            'day': timedelta(hours=n),
            'week': timedelta(hours=n),
            'month': relativedelta(months=n),
            'year': relativedelta(years=n),
        }
        return (self.retrieval_date - compare[split[1]]).strftime('%y/%m/%d')

    def __filter_string(self, str):
        strOut = str.replace('\r', ' ').replace('\n', ' ').replace('\t', ' ')
        return strOut
