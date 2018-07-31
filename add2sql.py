# -*- coding: utf-8 -*-
import os
import MySQLdb
import time
import random
import pdb
import sys

conn = MySQLdb.connect(host='127.0.0.1', port=3306, user='root', passwd='admin', db='labelattr', charset='utf8')
cursor = conn.cursor()

# get all attributes existing in the img_labels
#img_attrs = ['carTail', 'carColor', 'carDirection', 'carType', 'plateType']
img_attrs = ['plateNum']

# create 3 tables: users, user_label, img_label

# add users
user_dict = {'xiaohui' : 'password',
             'zhouhuijuan' : 'password',
             'qinqin' : 'password',
             'zhengzhenpeng' : 'password',
             'yanchao': 'password'
             }
for key, value in user_dict.items():
    temp_sql = "select Id from users where username='{}';".format(key)
    cursor.execute(temp_sql)
    rows = cursor.fetchall()
    if len(rows)==0:
        temp_sql = "insert into users(username, passwd) values ('{}', '{}');".format(key, value)
        cursor.execute(temp_sql)
    else:
        print("User: {} has already existed. Skip this user.".format(key))

# select all existing users, then will randomly distribute the images to them
temp_sql = "select Id from users"
cursor.execute(temp_sql)
rows = cursor.fetchall()
userId = [row[0] for row in rows]

# get img_list, and initialize the distribution
img_names_file = '/var/www/html/dataset.txt'
img_list = []
for line in open(img_names_file):
    line = line.strip()
    img_list.append(line)

pdb.set_trace()

# add items into table img_label and user_label
n = 0
for line in img_list:
    n += 1
    if(n % 100 == 0):
        print('Writing {}th image:{} to database.'.format(n, line))
    line = line.split()
    line = line[0].split('/')
    imgname = line.pop()
    data_source = '/'.join(line)
    try:
        # need to verify the img has not been added to the database before
        temp_sql = "select Id from img_label where image_name='{}' and data_source='{}';".format(imgname, data_source)
        cursor.execute(temp_sql)
        rows = cursor.fetchall()
        if len(rows) == 0:
            imglabel_sql = "INSERT INTO img_label(image_name, data_source) VALUES ('{}', '{}');".format(imgname, data_source)
            effect_row = cursor.execute(imglabel_sql)
            conn.commit()
            if effect_row != 1:
                print('database insert error')
        else:
            print('{} has been added into database.'.format(imgname))

        # get the Id in img_label according to image_name and its source
        userlabel_sql = "select Id from img_label where image_name='{}' and data_source='{}';".format(imgname, data_source)
        cursor.execute(userlabel_sql)
        rows = cursor.fetchall()
        assert len(rows) == 1
        imgId = rows[0][0]

        # add user-img info to user_label
        for img_attr in img_attrs:
            # check the item to be inserted has not already existed
            img_attr_check = "select Id from user_label where imgid='{:d}' and attr='{}';".format(imgId, img_attr)
            cursor.execute(img_attr_check)
            rows = cursor.fetchall()
            if len(rows)==0:
                userId_random = random.choice(userId)
                userlabel_sql = "insert into user_label(userid, imgid, attr) values ('{:d}', '{:d}', '{}');".format(userId_random, imgId, img_attr)
                #print(userlabel_sql)
                effect_row = cursor.execute(userlabel_sql)
                conn.commit()
                if effect_row != 1:
                    print('database insert error')
            else:
                print("Item of imgid:{:d} attr:{} has already existed. Skip this one.".format(imgId, img_attr))
    except:
        conn.rollback()
        print('database insert exception')

'''
# according to the existing users, re-distribute the images which have not been labeled to them randomly
temp_sql = 'select Id from user_label where label_value is NULL'
cursor.execute(temp_sql)
results = cursor.fetchall()
pdb.set_trace()
n = 0
for row in results:
    temp_sql = 'update user_label set userid = "{}" where Id = "{}"'.format(random.choice(userId), row[0])
    try:
        cursor.execute(temp_sql)
        conn.commit()
        n += 1
    except:
        conn.rollback()
print("Update {} items".format(n))
'''

conn.close()
