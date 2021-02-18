import logging, sys
FORMATTER = logging.Formatter('%(asctime)s - %(module)s (%(funcName)s) - %(levelname)s - %(message)s')
LOG_FILE = "logger.log"

def get_console_handler():
    console_handler = logging.StreamHandler(sys.stdout)
    console_handler.setFormatter(FORMATTER)
    return console_handler

def get_file_handler():
    file_handler = logging.FileHandler(LOG_FILE)
    file_handler.setFormatter(FORMATTER)
    return file_handler

def get_logger(name, mode = "file"):
   logger = logging.getLogger(name)
   logger.setLevel(logging.DEBUG)
   if (mode in ['console', 'both']): logger.addHandler(get_console_handler())
   if (mode in ['file', 'both']):logger.addHandler(get_file_handler())
   logger.propagate = False
   return logger
