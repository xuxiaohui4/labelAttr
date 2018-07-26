import os
import pymysql
import time
from IPython import embed
import pdb

conn = pymysql.connect(host='127.0.0.1', port=3306, user='root', passwd='199010', db='labelattr', charset='utf8')
cursor = conn.cursor()
gender = 'gender'
userid=1
query_sql = "select imgid, label_value from user_label where attr='%s' and userid='%d'" % (gender, userid)
cursor.execute(query_sql)
query_result = cursor.fetchall()
pdb.set_trace()
for row in query_result:
    img_id = row[0]
    label_value = row[1]
    update_sql = "update img_label set gender='%d', gender_labeled='%d' where Id='%d'" % (label_value, 1, img_id)
    try:
        cursor.execute(update_sql)
    except:
        print('update error')

