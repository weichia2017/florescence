from datetime import datetime
from datetime import timedelta
from dateutil.relativedelta import relativedelta


class GoogleReview:
    def __init__(self, review):
        self.review_id = review.find(
            'button', class_='section-review-action-menu')['data-review-id']
        try:
            review_text = self.__filter_string(review.find(
                'span', class_='section-review-text').text)
        except Exception as e:
            review_text = None
        self.review_text = review_text
        self.relative_date = review.find(
            'span', class_='section-review-publish-date').text
        self.retrieval_date = datetime.now()
        self.review_date = self.getEstimatedDate()
        # self.rating = float(review.find('span', class_='section-review-stars')['aria-label'].split(' ')[1])
        # self.username = review.find('div', class_='section-review-title').find('span').text
        # try:
        #     n_reviews_photos = review.find('div', class_='section-review-subtitle').find_all('span')[1].text
        #     metadata = n_reviews_photos.split('\xe3\x83\xbb')
        #     if len(metadata) == 3:
        #         n_photos = int(metadata[2].split(' ')[0].replace('.', ''))
        #     else:
        #         n_photos = 0
        #     idx = len(metadata)
        #     self.n_review_user = int(metadata[idx - 1].split(' ')[0].replace('.', ''))
        #     self.n_photo_user = n_photos
        # except Exception as e:
        #     self.n_review_user = 0
        #     self.n_photo_user = 0
        # self.user_url = review.find('a')['href']

    def setStoreID(self, store_id):
        self.store_id = store_id

    def getEstimatedDate(self):
        split = self.relative_date.split()
        n = 1 if split[0][0] == 'a' else int(split[0])
        split[1] = split[1][:-1] if split[1][-1] == "s" else split[1]
        compare = {
            'second': timedelta(seconds=n),
            'minute': timedelta(minutes=n),
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
