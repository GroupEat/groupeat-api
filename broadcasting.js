const port = 3000;

const moment = require('moment');
const now = () => `[${moment().format('YYYY-MM-DD HH:mm:ss')}]`;

require('dotenv').load();
const pgsql = require('pg');
const pgsqlPassword = process.env.PGSQL_PASSWORD || 'groupeat';
const pgsqlUrl = `postgres://groupeat:${pgsqlPassword}@127.0.0.1/groupeat`;

function handler(req, res) {
  res.writeHead(200);
  res.end('');
};

var app;

try {
  const fs = require('fs');
  const sslOptions = {
    key: fs.readFileSync(`/etc/letsencrypt/live/current/privkey.pem`),
    cert: fs.readFileSync(`/etc/letsencrypt/live/current/fullchain.pem`)
  };

  app = require('https').createServer(sslOptions, handler);
  console.log(now(), 'server using https');
} catch (e) {
  app = require('http').createServer(handler);
  console.log(now(), 'server using http');
}

const io = require('socket.io')(app);

io.set('origins', '*:*');
app.listen(port, () => { console.log(now(), `server running on port ${port}`); });

require('socketio-auth')(io, {
  authenticate: (socket, data, callback) => {
    var msg;
    const token = data.token;
    const client = new pgsql.Client(pgsqlUrl);
    const ip = () => `{"IP":"${socket.request.connection.remoteAddress}"}`;

    client.connect(err => {
      if (err) {
        msg = 'could not connect to database';
        callback(new Error(msg));

        return console.error(now(), msg, err, ip());
      }

      client.query(`SELECT * FROM "user_credentials" WHERE "token" = '${token}'`, (err, result) => {
        client.end();

        if (err) {
          msg = 'error running query';
          callback(new Error(msg));

          return console.error(now(), msg, err, ip());
        }

        if (result.rows.length) {
          const userId = result.rows[0].id;
          callback(null, true);
          socket.join(userId);

          return console.log(now(), `user#${userId} connected`, ip());
        }

        msg = 'bad token';
        callback(new Error(msg));

        return console.error(now(), msg, token, ip());
      });
    });
  }
});

const Redis = require('ioredis');
const redis = new Redis();

redis.psubscribe('*', (err, count) => {});

redis.on('pmessage', (pattern, room, message) => {
  message = JSON.parse(message);
  console.log(now(), `room#${room}`, message);
  io.to(room).emit(message.event, message.data);
});
