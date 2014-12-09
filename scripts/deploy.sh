#!/usr/bin/env bash

ssh vagrant@178.62.158.190 "bash -s" < $( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )/production/deploying.sh
