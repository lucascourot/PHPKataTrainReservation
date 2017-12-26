"""
This is a guiding test for this kata. You can run it using python 3.3 like this:

    python3 -m unittest guiding_test.py
"""

import urllib.request
import json
import subprocess
import unittest
import os

url = "http://127.0.0.1:8083"
interpreter = "python3"

class TrainReservationTest(unittest.TestCase):

    def test_reserve_seats_via_POST(self):
        form_data = {"train_id": "express_2000", "seat_count": 4}
        data = urllib.parse.urlencode(form_data)

        req = urllib.request.Request(url + "/reserve", bytes(data, encoding="ISO-8859-1"))
        response = urllib.request.urlopen(req).read().decode("ISO-8859-1")
        reservation = json.loads(response)

        assert "express_2000" == reservation["train_id"]
        assert 4 == len(reservation["seats"])
        assert "1A" == reservation["seats"][0]
        assert "75bcd15" == reservation["booking_reference"]
