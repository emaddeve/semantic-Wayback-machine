from scrapy import Spider
from scrapy.selector import Selector

from stack.items import StackItem

import time
import scrapy
import requests
from scrapy.http import Request


from scrapy.spiders import CrawlSpider, Rule
from scrapy.linkextractors import LinkExtractor
URL = "http://www.example.com/id=%d"
starting_number = 2
number_of_pages = 500

URL = "http://stackoverflow.com/questions?page=%d&sort=newest"   


class StackSpider(Spider):
    name = "stack"
    allowed_domains = ["stackoverflow.com"]
    start_urls = [URL % starting_number]

    def __init__(self):
        self.page_number = starting_number
        
    def start_requests(self):
        # generate page IDs from 1000 down to 501
        for i in range (self.page_number, number_of_pages, +1):
            yield Request(url = URL % i, callback=self.parse) 

    def parse(self, response):
        self.page_number = 1
        questions = Selector(response).xpath('//div[@class="summary"]')
   

        for question in questions:
            item = StackItem()
            item['title'] = question.xpath(
                'h3/a[@class="question-hyperlink"]/text()').extract()[0]
                
            item['url'] ="http://stackoverflow.com"+question.xpath(
                'h3/a[@class="question-hyperlink"]/@href').extract()[0]
                
            item['tags']=question.xpath('div/a[@class="post-tag"][1]/text()').extract()[0]
            
            item['date']= time.strftime("%Y-%m-%dT%H:%M:%SZ")
            
          #  item['name'] = question.xpath(
           #     '//div[@class="started"]/a[2]/text()').extract()[0]
          
         #   item['date'] = question.xpath('div[@class="started"]/a/span/@title').extract()[0]
           
            yield item
           
            
