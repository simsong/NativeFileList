-- Creates the das_s3bucket database
CREATE DATABASE IF NOT EXISTS `das_s3bucket`;

-- Create the metadata table
DROP TABLE IF EXISTS `das_s3bucket`.metadata;
CREATE TABLE  `das_s3bucket`.metadata (name VARCHAR(255) PRIMARY KEY, value VARCHAR(255) NOT NULL);

-- Create the roots table
DROP TABLE IF EXISTS `das_s3bucket`.roots;
CREATE TABLE `das_s3bucket`.roots (rootid INTEGER PRIMARY KEY AUTO_INCREMENT, rootdir VARCHAR(255) NOT NULL);

-- Create dirnames table
DROP TABLE IF EXISTS `das_s3bucket`.dirnames;
CREATE TABLE  `das_s3bucket`.dirnames (dirnameid INTEGER PRIMARY KEY AUTO_INCREMENT,dirname TEXT(65536));
CREATE UNIQUE INDEX  dirnames_idx2 ON `das_s3bucket`.dirnames (dirname(700));

-- Create the filenames table
DROP TABLE IF EXISTS `das_s3bucket`.filenames;
CREATE TABLE  `das_s3bucket`.filenames (filenameid INTEGER PRIMARY KEY AUTO_INCREMENT,filename TEXT(65536));
CREATE INDEX  filenames_idx2 ON `das_s3bucket`.filenames (filename(700));

-- Creates the paths table
DROP TABLE IF EXISTS `das_s3bucket`.paths;
CREATE TABLE  `das_s3bucket`.paths (pathid INTEGER PRIMARY KEY AUTO_INCREMENT,
       dirnameid INTEGER REFERENCES `das_s3bucket`.dirnames(dirnameid),
       filenameid INTEGER REFERENCES `das_s3bucket`.filenames(filenameid));
CREATE INDEX  paths_idx2 ON `das_s3bucket`.paths(dirnameid);
CREATE INDEX  paths_idx3 ON `das_s3bucket`.paths(filenameid);

-- Creates hashes table
DROP TABLE IF EXISTS `das_s3bucket`.hashes;
CREATE TABLE  `das_s3bucket`.hashes (hashid INTEGER PRIMARY KEY AUTO_INCREMENT,hash TEXT(65536) NOT NULL);
CREATE INDEX  hashes_idx2 ON `das_s3bucket`.hashes( hash(700));

-- Creates the scanes table
DROP TABLE IF EXISTS `das_s3bucket`.scans;
CREATE TABLE  `das_s3bucket`.scans (scanid INTEGER PRIMARY KEY AUTO_INCREMENT,
                                      rootid INTEGER REFERENCES roots(rootid),
                                      time DATETIME NOT NULL UNIQUE,
                                      duration INTEGER);
CREATE INDEX  scans_idx1 ON `das_s3bucket`.scans(scanid);
CREATE INDEX  scans_idx2 ON `das_s3bucket`.scans(time);

-- Creates the files table
DROP TABLE IF EXISTS `das_s3bucket`.files;
CREATE TABLE  `das_s3bucket`.files (fileid INTEGER PRIMARY KEY AUTO_INCREMENT,
                                  pathid INTEGER REFERENCES paths(pathid),
                                  rootid INTEGER REFERENCES roots(rootid),
                                  mtime INTEGER NOT NULL, 
                                  size INTEGER NOT NULL, 
                                  hashid INTEGER REFERENCES hashes(hashid), 
                                  scanid INTEGER REFERENCES scans(scanid));
CREATE INDEX  files_idx1 ON `das_s3bucket`.files(pathid);
CREATE INDEX  files_idx2 ON `das_s3bucket`.files(rootid);
CREATE INDEX  files_idx3 ON `das_s3bucket`.files(mtime);
CREATE INDEX  files_idx4 ON `das_s3bucket`.files(size);
CREATE INDEX  files_idx5 ON `das_s3bucket`.files(hashid);
CREATE INDEX  files_idx6 ON `das_s3bucket`.files(scanid);
CREATE INDEX  files_idx7 ON `das_s3bucket`.files(scanid,hashid);