const port = 3000;

function handler(req, res) {
    res.writeHead(200);
    res.end('');
};

const app = require('http').createServer(handler);
const io = require('socket.io')(app);

const Redis = require('ioredis');
const redis = new Redis();

app.listen(port, () => {
    console.log(`Server running on port ${port}`);
});

io.on('connection', socket => {});

redis.psubscribe('*', (err, count) => {});

redis.on('pmessage', (pattern, channel, message) => {
    message = JSON.parse(message);
    console.log(channel, message);
    io.emit(channel + ':' + message.event, message.data);
});
