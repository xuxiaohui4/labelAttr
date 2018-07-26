import os
import json
import pymysql

conn = pymysql.connect(host='127.0.0.1', port=3306, user='root', passwd='pkumi', db='labelattr', charset='utf8')
cursor = conn.cursor()

img_names_file = 'label_data_path_name.txt'
json_file = 'ccf_annotations.json'
img_list = []
labels = json.load(open(json_file))

for line in open(img_names_file):
    line = line.strip()
    if line[0] == 'C':
        if line[1:6] == '00001':
            img_list.append(line)

for imgname in img_list:
    print(imgname)
    ori_name = 'IMG_'+imgname.split('_')[1]+'.jpg'
    data_source = imgname[0]
    ccf_gender = labels[ori_name]['gender']
    if ccf_gender == 'NULL':
        ccf_gender = 2
    else:
        ccf_gender = int(ccf_gender)
    ccf_hair = labels[ori_name]['hairlength']
    if ccf_hair == 'NULL':
        ccf_hair = 2
    else:
        ccf_hair = int(ccf_hair)
    ccf_hat = labels[ori_name]['hat']
    if ccf_hat == 'NULL':
        ccf_hat = 3
    else:
        ccf_hat = int(ccf_hat)
    ccf_top_category = labels[ori_name]['top_category']
    if ccf_top_category == 'NULL':
        ccf_top_category = 5
    else:
        ccf_top_category = int(ccf_top_category)
    ccf_top_color = labels[ori_name]['top_color']
    if ccf_top_color == 'NULL':
        ccf_top_color = 11
    else:
        ccf_top_color = int(ccf_top_color)
    ccf_down_category = labels[ori_name]['down_category']
    if ccf_down_category == 'NULL':
        ccf_down_category = 4
    else:
        ccf_down_category = int(ccf_down_category)
    ccf_down_color = labels[ori_name]['down_color']
    if ccf_down_color == 'NULL':
        ccf_down_color = 11
    else:
        ccf_down_color = int(ccf_down_color)

    if labels[ori_name]['bag_category'] == 'NULL':
        ccf_bag_category = 5
    else:
        ccf_bag_category = int(labels[ori_name]['bag_category'])
    if ccf_bag_category in [0, 1]:
        bag = ccf_bag_category + 1
        bag_labeled = 1
    else:
        bag = 0
        bag_labeled = 0
    gender = ccf_gender
    gender_labeled = 1
    hairtype = ccf_hair
    hairtype_labeled = 1
    hattype = ccf_hat
    if hattype == 0:
        hattype_labeled = 1
    else:
        hattype_labeled = 0
    if ccf_top_category in [2, 3, 4]:
        top_category = 0
        top_category_labeled = 1
    else:
        top_category = 1
        top_category_labeled = 0
    if ccf_top_color in [10, 11]:
        top_color = 10
        top_color_labeled = 1
    else:
        top_color = ccf_top_color
        top_color_labeled = 1
    
    down_category_labeled = 1
    if ccf_down_category in [0, 2]:
        down_category = 0
    elif ccf_down_category in [1, 3]:
        down_category = 1
    else:
        down_category = 2
    if ccf_down_color in [10, 11]:
        down_color = 10
        down_color_labeled = 1
    else:
        down_color = ccf_down_color
        down_color_labeled = 1
   
    tmp_sql = "insert into img_label(image_name, data_source, ischoosed, choose_labeled, gender, gender_labeled, hattype, hattype_labeled, hair, hair_labeled, coatcolor, coatcolor_labeled, coattype, coattype_labeled, trouserscolor, trouserscolor_labeled, trouserstype, trouserstype_labeled, bag, bag_labeled) values ('%s', '%s', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d');" % (imgname, data_source, 1, 1, gender, gender_labeled, hattype, hattype_labeled, hairtype, hairtype_labeled, top_color, top_color_labeled, top_category, top_category_labeled, down_color, down_color_labeled, down_category, down_category_labeled, bag, bag_labeled)
    try:
        effect_row = cursor.execute(tmp_sql)
        conn.commit()
        if effect_row != 1:
            print('database insert error')
    except:
       print('database insert exception')
