# PHP Protocol Buffers

This is a PHP Google Protocol Buffer Generator Plugin for protoc. It generates PHP sourcecode from a .proto file. It's not finished, but supports most common features, and is currently in use in production systems.

## Installation

1. Download and install the Protocol Buffer source from http://code.google.com/p/protobuf/downloads/list
2. Build and install the Protocol Buffer source ([see instructions in source](http://code.google.com/p/protobuf/source/browse/trunk/INSTALL.txt))
3. [Download this repo](http://github.com/iamamused/protoc-gen-php/archives/master).
4. `cd` to this repo's source and type "make". 

** NOTE, 

### System specifics

On Debian you may need the install a few libraries with apt apt:

	apt-get install libprotobuf-dev libprotobuf-lite6 libprotobuf6 libprotoc-dev libprotoc6



## Usage
Once compiled you can use it via protoc like so:

	protoc -I. -I/usr/include --php_out . --plugin=protoc-gen-php=./protoc-gen-php your.proto

This will generate the file "YourProto.php", which will encode and decode protocol buffer messages. When using the generated PHP code you must include the "protocolbuffers.inc.php" file.

## TODO's
* Better documentation
* Better exception handling
* Some kind of inheritance model to reduce generated PHP code
* option (php_multiple_files) support.
* option (php_package) support
* `make install` to a bash wrapper so that we can do something like `protocphp your.proto`  

## Authors
* Original source by Andrew Brampton (c) 2010 [http://github.com/bramp/protoc-gen-php](http://github.com/bramp/protoc-gen-php)
* This fork updates by Jeffrey Sambells
