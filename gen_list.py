# -*- coding:utf-8 -*-
import os
import sys
import pdb
    
def travelfolder(rootDir, file_list = []):
    allfilelist = os.listdir(rootDir)
    for _file in allfilelist:
        filepath = os.path.join(rootDir, _file)
        if os.path.isdir(filepath):
            travelfolder(filepath, file_list)
        else:
            file_list.append(filepath) 
    return file_list

des_file = '../dataset.txt'
img_list = []
rootDir = '../Dataset'
img_list = travelfolder(rootDir, img_list)
with open(des_file, 'w') as f:
    for line in img_list:
        if line.endswith('.jpg'):
            f.write(line+'\n')
