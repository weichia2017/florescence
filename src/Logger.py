import logging
import sys
FORMATTER = logging.Formatter(
    '%(asctime)s - %(module)s (%(funcName)s) - %(levelname)s - %(message)s')
LOG_FILE = "logger.log"
CONSOLE = False
FILE = True


def get_console_handler():
    console_handler = logging.StreamHandler(sys.stdout)
    console_handler.setFormatter(FORMATTER)
    return console_handler


def get_file_handler():
    file_handler = logging.FileHandler(LOG_FILE)
    file_handler.setFormatter(FORMATTER)
    return file_handler


def get_logger(name):
    logger = logging.getLogger(name)
    logger.setLevel(logging.DEBUG)
    if CONSOLE:
        logger.addHandler(get_console_handler())
    if FILE:
        logger.addHandler(get_file_handler())
    logger.propagate = False
    return logger
