#!/usr/bin/python3 -u

import serial, time

MODE_COMMAND = '\xb4'
MODE_RESPONSE = '\xc5'

CMD_DATA_REPORT_MODE = 2
CMD_QUERY_DATA = 4
CMD_SET_DEVICE_ID = 5
CMD_SET_SLEEP_AND_WORK = 6
CMD_SET_WORKING_PERIOD = 8
CMD_CHECK_FIRMWARE_VERSION = 7

pm25 = []
pm10 = []

def gen_check_sum(data):
    sum = 0
    for d in data:
        sum = ord(d) + sum

    return sum % 256

def gen_payload(cmd, data):
    payload = [chr(0)] * 19
    payload[0] = '\xaa'
    payload[1] = MODE_COMMAND
    payload[2] = chr(cmd)
    i = 3
    for d in data:
       payload[i] = chr(d)
       i = i + 1
    payload[15] = '\xff'
    payload[16] = '\xff'
    payload[17] = chr(gen_check_sum(payload[2:17])) 
    payload[18] = '\xab'
    return payload

def send_command(payload):
    payload_unicode = ''.join(payload)
    ser.write(payload_unicode.encode('latin-1'))

def read_response():
    enter = 0
    while enter != b"\xaa":
        enter = ser.read()
    d = ser.read(9)
    response = enter + d
    return response

def cmd_set_work():
    data = [
            1, # Set Mode
            1  # Work
            ]
    send_command(gen_payload(CMD_SET_SLEEP_AND_WORK, data))
    read_response()

def cmd_set_sleep():
    data = [
            1, # Set mode
            0  # Sleep
           ]
    send_command(gen_payload(CMD_SET_SLEEP_AND_WORK, data))
    read_response()

def process_data(string):
    lowpm = string[2]
    highpm = string[4]
    lowpm = int(lowpm)
    highpm = int(highpm)
    pm25.append(lowpm/10)
    pm10.append(highpm/10)

def cmd_query_data():
    data = [0,0]
    send_command(gen_payload(CMD_QUERY_DATA, data))
    response = read_response()
    process_data(response)

def print_data():
    total = 0
    pm25.pop(0)
    for i in pm25:
        total = total + i
    pm25_avg = round(total / len(pm25), 1)

    total = 0
    pm10.pop(0)
    for i in pm10:
        total = total + i
    pm10_avg = round(total / len(pm10), 1)

    print("PM2.5 > " + str(pm25_avg) + " PM10 > " + str(pm10_avg))




ser = serial.Serial("/dev/ttyUSB0")

ser.flushInput()
print("Writing to " + ser.name)
ser.baudrate = 9600
ser.flushInput()

cmd_set_work()

for i in range(1, 7):
  cmd_query_data()
  time.sleep(1)
  
cmd_set_sleep()

ser.close()

print_data()
