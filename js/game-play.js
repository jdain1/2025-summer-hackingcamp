// 서버값
const TIME_LIMIT = 10.0;

const playerEl = document.getElementById('player');
const wallEl = document.getElementById('wall');
const gameArea = document.getElementById('gameArea');
const timeEl = document.getElementById('time');
const noticeEl = document.getElementById('notice');
const retryBtn = document.getElementById('retryBtn');

let timeLeft = TIME_LIMIT;
let lastTs = null;
let movingRight = false;
let gameOver = false;
let initialLeft = null; // 초기 left 값(px)

// 키 이벤트
window.addEventListener('keydown', (e) => {
  if (e.key === 'ArrowRight') movingRight = true;
});
window.addEventListener('keyup', (e) => {
  if (e.key === 'ArrowRight') movingRight = false;
});

// 충돌 계산용
function getRects() {
  const p = playerEl.getBoundingClientRect();
  const w = wallEl.getBoundingClientRect();
  const g = gameArea.getBoundingClientRect();
  return { player:p, wall:w, area:g };
}

// 게임 루프
function loop(ts) {
  if (!lastTs) lastTs = ts;
  const dt = (ts - lastTs) / 1000;
  lastTs = ts;
  if (gameOver) return;

  timeLeft = Math.max(0, timeLeft - dt);
  timeEl.textContent = timeLeft.toFixed(1);

  if (movingRight && timeLeft > 0) {
    const style = window.getComputedStyle(playerEl);
    const leftPx = parseFloat(style.left);
    const delta = serverSpeed * dt;
    playerEl.style.left = (leftPx + delta) + 'px';
  }

  const r = getRects();
  const playerRight = r.player.left + r.player.width;
  const wallLeft = r.wall.left;
  if (playerRight >= wallLeft) {
    gameOver = true;
    // 성공이면 flag로 이동
    noticeEl.textContent = 'clear! flag = ...';
    return;
  }

  if (timeLeft <= 0) {
    gameOver = true;
    noticeEl.textContent = '시간 초과! retry 하십시오.';
    showRetry();
    return;
  }

  requestAnimationFrame(loop);
}

// Retry 보여주기
function showRetry() {
  retryBtn.style.display = 'inline-block';
}

// 게임 리셋 (Retry)
function resetGame() {
  // 초기화
  timeLeft = TIME_LIMIT;
  lastTs = null;
  movingRight = false;
  gameOver = false;
  noticeEl.textContent = '';
  timeEl.textContent = timeLeft.toFixed(1);
  retryBtn.style.display = 'none';

  // 플레이어 위치 초기화
  if (initialLeft !== null) {
    playerEl.style.left = initialLeft + 'px';
  }

  // 재시작
  requestAnimationFrame(loop);
}

// 초기화: 초기 좌표, 거리 계산, 요구 속도 안내
function init() {
  // 초기 left 값을 픽셀 단위로 저장
  const style = window.getComputedStyle(playerEl);
  initialLeft = parseFloat(style.left);

  const r = getRects();
  const distance = r.wall.left - (r.player.left + r.player.width);
  const requiredSpeed = distance / TIME_LIMIT;

  requestAnimationFrame(loop);
}

// Retry 버튼 클릭 이벤트
retryBtn.addEventListener('click', function() {
  resetGame();
});

// 추가: Enter 키로도 재시작 가능 (선택)
window.addEventListener('keydown', function(e) {
  if (e.key === 'Enter' && retryBtn.style.display !== 'none') {
    resetGame();
  }
});

window.addEventListener('load', init);