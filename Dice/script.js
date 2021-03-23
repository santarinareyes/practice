'use strict';

// Variables
const p1roundScore = document.querySelector('#score--0');
const p2roundScore = document.querySelector('#score--1');
const dice = document.querySelector('.dice');
const btnNew = document.querySelector('.btn--new');
const btnRoll = document.querySelector('.btn--roll');
const btnHold = document.querySelector('.btn--hold');
const p1currentScore = document.getElementById('current--0');
const p2currentScore = document.getElementById('current--1');
const p1winner = document.getElementById('winner--0');
const p2winner = document.getElementById('winner--1');
const p1 = document.querySelector('.player--0');
const p2 = document.querySelector('.player--1');

////////////////////////////////////////////////////////////////////
let p1numWins = 0;
let p2numWins = 0;
let scores, currentScore, activePlayer;

function play() {
  scores = [0, 0];
  currentScore = 0;
  activePlayer = 0;

  p1roundScore.textContent = 0;
  p2roundScore.textContent = 0;
  p1currentScore.textContent = 0;
  p2currentScore.textContent = 0;

  btnHold.disabled = false;
  btnRoll.disabled = false;

  p1winner.classList.add('hidden');
  p2winner.classList.add('hidden');
  p1.classList.remove('player--winner');
  p2.classList.remove('player--winner');
  p1.classList.add('player--active');
  p2.classList.remove('player--active');
  dice.classList.add('hidden');
}
play();

////////////////////////////////////////////////////////////////////

btnRoll.addEventListener('click', function () {
  const diceRolled = Math.trunc(Math.random() * 6) + 1;

  dice.classList.remove('hidden');
  dice.src = `dice-${diceRolled}.png`;

  if (diceRolled !== 1) {
    currentScore += diceRolled;
    document.getElementById(
      `current--${activePlayer}`
    ).textContent = currentScore;
  } else {
    switchPlayer();
  }
});

btnHold.addEventListener('click', function () {
  scores[activePlayer] += currentScore;
  document.getElementById(`score--${activePlayer}`).textContent =
    scores[activePlayer];

  if (scores[activePlayer] >= 10) {
    document
      .querySelector(`.player--${activePlayer}`)
      .classList.add('player--winner');
    document
      .querySelector(`.player--${activePlayer}`)
      .classList.remove('player--active');
    document
      .querySelector(`#winner--${activePlayer}`)
      .classList.remove('hidden');
    btnHold.disabled = true;
    btnRoll.disabled = true;
    dice.classList.add('hidden');

    if (activePlayer === 0) {
      p1numWins++;
      document.querySelector(`#wins--${activePlayer}`).textContent = p1numWins;
    } else {
      p2numWins++;
      document.querySelector(`#wins--${activePlayer}`).textContent = p2numWins;
    }
  } else {
    switchPlayer();
  }
});

function switchPlayer() {
  document.getElementById(`current--${activePlayer}`).textContent = 0;
  currentScore = 0;
  activePlayer = activePlayer === 0 ? 1 : 0;
  p1.classList.toggle('player--active');
  p2.classList.toggle('player--active');
}

btnNew.addEventListener('click', play);
