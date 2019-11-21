-- Creates the das_s3bucket database
CREATE DATABASE IF NOT EXISTS `das_s3bucket`;

-- Create the metadata table
DROP TABLE IF EXISTS /*_*/metadata;
CREATE TABLE  /*_*/metadata (name VARCHAR(255) PRIMARY KEY, value VARCHAR(255) NOT NULL);

-- Create the roots table
DROP TABLE IF EXISTS /*_*/roots;
CREATE TABLE /*_*/roots (rootid INTEGER PRIMARY KEY AUTO_INCREMENT, rootdir VARCHAR(255) NOT NULL);

-- Create dirnames table
DROP TABLE IF EXISTS /*_*/dirnames;
CREATE TABLE  /*_*/dirnames (dirnameid INTEGER PRIMARY KEY AUTO_INCREMENT,dirname TEXT(65536));
CREATE UNIQUE INDEX  /*i*/dirnames_idx2 ON /*_*/dirnames (dirname(700));

-- Create the filenames table
DROP TABLE IF EXISTS /*_*/filenames;
CREATE TABLE  /*_*/filenames (filenameid INTEGER PRIMARY KEY AUTO_INCREMENT,filename TEXT(65536));
CREATE INDEX  /*i*/filenames_idx2 ON /*_*/filenames (filename(700));

-- Creates the paths table
DROP TABLE IF EXISTS /*_*/paths;
CREATE TABLE  /*_*/paths (pathid INTEGER PRIMARY KEY AUTO_INCREMENT,
       dirnameid INTEGER REFERENCES /*_*/dirnames(dirnameid),
       filenameid INTEGER REFERENCES /*_*/filenames(filenameid));
CREATE INDEX  /*i*/paths_idx2 ON /*_*/.paths(dirnameid);
CREATE INDEX  /*i*/paths_idx3 ON /*_*/(filenameid);

-- Creates hashes table
DROP TABLE IF EXISTS /*_*/hashes;
CREATE TABLE  /*_*/hashes (hashid INTEGER PRIMARY KEY AUTO_INCREMENT,hash TEXT(65536) NOT NULL);
CREATE INDEX  /*i*/hashes_idx2 ON /*_*/hashes( hash(700));

-- Creates the scanes table
DROP TABLE IF EXISTS /*_*/scans;
CREATE TABLE  /*_*/scans (scanid INTEGER PRIMARY KEY AUTO_INCREMENT,
                                      rootid INTEGER REFERENCES roots(rootid),
                                      time DATETIME NOT NULL UNIQUE,
                                      duration INTEGER);
CREATE INDEX  /*i*/scans_idx1 ON /*_*/scans(scanid);
CREATE INDEX  /*i*/scans_idx2 ON /*_*/scans(time);

-- Creates the files
DROP TABLE IF EXISTS /*_*/files;
CREATE TABLE  /*_*/files (fileid INTEGER PRIMARY KEY AUTO_INCREMENT,
                                  pathid INTEGER REFERENCES paths(pathid),
                                  rootid INTEGER REFERENCES roots(rootid),
                                  mtime INTEGER NOT NULL, 
                                  size INTEGER NOT NULL, 
                                  hashid INTEGER REFERENCES hashes(hashid), 
                                  scanid INTEGER REFERENCES scans(scanid));
CREATE INDEX  /*i*/files_idx1 ON /*_*/files(pathid);
CREATE INDEX  /*i*/files_idx2 ON /*_*/files(rootid);
CREATE INDEX  /*i*/files_idx3 ON /*_*/files(mtime);
CREATE INDEX  /*i*/files_idx4 ON /*_*/files(size);
CREATE INDEX  /*i*/files_idx5 ON /*_*/files(hashid);
CREATE INDEX  /*i*/files_idx6 ON /*_*/files(scanid);
CREATE INDEX  /*i*/files_idx7 ON /*_*/files(scanid,hashid);