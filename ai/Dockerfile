FROM python:3.8

WORKDIR /usr/src/ai

COPY . /usr/src/ai

RUN pip install flask

EXPOSE 5000

CMD ["flask", "--app", "app", "run", "--host=0.0.0.0"]