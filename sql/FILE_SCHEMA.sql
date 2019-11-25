-- Create the metadata table
DROP TABLE IF EXISTS /*_*/metadata;
CREATE TABLE  /*_*/metadata (name VARCHAR(255) PRIMARY KEY, value VARCHAR(255) NOT NULL);

-- Create the roots table
DROP TABLE IF EXISTS /*_*/nfl_roots;
CREATE TABLE /*_*/nfl_roots (rootid INTEGER PRIMARY KEY AUTO_INCREMENT, rootdir VARCHAR(255) NOT NULL);

-- Create dirnames table
DROP TABLE IF EXISTS /*_*/nfl_dirnames;
CREATE TABLE  /*_*/nfl_dirnames (dirnameid INTEGER PRIMARY KEY AUTO_INCREMENT,dirname TEXT(65536));
CREATE UNIQUE INDEX  /*i*/dirnames_idx2 ON /*_*/nfl_dirnames (dirname(700));

-- Create the filenames table
DROP TABLE IF EXISTS /*_*/nfl_filenames;
CREATE TABLE  /*_*/nfl_filenames (filenameid INTEGER PRIMARY KEY AUTO_INCREMENT,filename TEXT(65536));
CREATE INDEX  /*i*/filenames_idx2 ON /*_*/nfl_filenames (filename(700));

-- Creates the paths table
DROP TABLE IF EXISTS /*_*/nfl_paths;
CREATE TABLE  /*_*/nfl_paths (pathid INTEGER PRIMARY KEY AUTO_INCREMENT,
       dirnameid INTEGER REFERENCES /*_*/nfl_dirnames(dirnameid),
       filenameid INTEGER REFERENCES /*_*/nfl_filenames(filenameid));
CREATE INDEX  /*i*/paths_idx2 ON /*_*/nfl_paths(dirnameid);
CREATE INDEX  /*i*/paths_idx3 ON /*_*/nfl_paths(filenameid);

-- Creates hashes table
DROP TABLE IF EXISTS /*_*/nfl_hashes;
CREATE TABLE  /*_*/nfl_hashes (hashid INTEGER PRIMARY KEY AUTO_INCREMENT,hash TEXT(65536) NOT NULL);
CREATE INDEX  /*i*/hashes_idx2 ON /*_*/nfl_hashes( hash(700));

-- Creates the scanes table
DROP TABLE IF EXISTS /*_*/nfl_scans;
CREATE TABLE  /*_*/nfl_scans (scanid INTEGER PRIMARY KEY AUTO_INCREMENT,
                                      rootid INTEGER REFERENCES nfl_roots(rootid),
                                      time DATETIME NOT NULL UNIQUE,
                                      duration INTEGER);
CREATE INDEX  /*i*/scans_idx1 ON /*_*/nfl_scans(scanid);
CREATE INDEX  /*i*/scans_idx2 ON /*_*/nfl_scans(time);

-- Creates the files
DROP TABLE IF EXISTS /*_*/nfl_files;
CREATE TABLE  /*_*/nfl_files (fileid INTEGER PRIMARY KEY AUTO_INCREMENT,
                                  pathid INTEGER REFERENCES nfl_paths(pathid),
                                  rootid INTEGER REFERENCES nfl_roots(rootid),
                                  mtime INTEGER NOT NULL, 
                                  size INTEGER NOT NULL, 
                                  hashid INTEGER REFERENCES nfl_hashes(hashid), 
                                  scanid INTEGER REFERENCES nfl_scans(scanid));
CREATE INDEX  /*i*/files_idx1 ON /*_*/nfl_files(pathid);
CREATE INDEX  /*i*/files_idx2 ON /*_*/nfl_files(rootid);
CREATE INDEX  /*i*/files_idx3 ON /*_*/nfl_files(mtime);
CREATE INDEX  /*i*/files_idx4 ON /*_*/nfl_files(size);
CREATE INDEX  /*i*/files_idx5 ON /*_*/nfl_files(hashid);
CREATE INDEX  /*i*/files_idx6 ON /*_*/nfl_files(scanid);
CREATE INDEX  /*i*/files_idx7 ON /*_*/nfl_files(scanid,hashid);