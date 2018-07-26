# -*- coding: utf-8 -*-
import os
import MySQLdb
import time
import random
import pdb
import sys

reload(sys)
sys.setdefaultencoding('utf8')

# build the connection to mysql
conn = MySQLdb.connect(host='127.0.0.1', port=3306, user='root', passwd='admin', db='labelattr', charset='utf8')
cursor = conn.cursor()
cursor.execute('SET NAMES UTF8')

# export data whose squarePlate value is 0 into squareplate.txt
des_file = 'squareplate.txt'
temp_sql = "select image_name from img_label where squarePlate=0;"
cursor.execute(temp_sql)
rows = cursor.fetchall()
rows = [row[0] for row in rows]

with open(des_file, 'w') as f:
    for row in rows:
        f.write(row+'\n')

conn.close()
