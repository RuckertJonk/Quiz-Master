// quizanswer.js - schone, werkende quiz-logica

// State
let totalScore = 0;
let answered = false;

// DOM elementen
const questionText = document.getElementById('question_text');
const optionsContainer = document.getElementById('question_options');
const submitBtn = document.getElementById('submitBtn');
const feedback = document.getElementById('feedback');
const scoreValue = document.getElementById('totalScore');

// Vragenlijst (eenvoudige velden voor latere DB-integratie)
const questions = [
  { id: 1, text: 'Wat is de hoofdstad van Frankrijk?', options: ['Londen','Parijs','Berlijn','Madrid'], correctIndex: 1 },
  { id: 2, text: 'Welke taal is de officiële taal van Brazilië?', options: ['Spaans','Portugees','Frans','Engels'], correctIndex: 1 },
  { id: 3, text: 'Welke planeet staat het dichtst bij de zon?', options: ['Venus','Aarde','Mercurius','Mars'], correctIndex: 2 },
  { id: 4, text: 'Hoeveel centimeter zitten er in een meter?', options: ['10','100','1000','10000'], correctIndex: 1 },
  { id: 5, text: 'Wie schreef "Romeo en Julia"?', options: ['Dante','Shakespeare','Homerus','Goethe'], correctIndex: 1 },
  { id: 6, text: 'Wat is H2O?', options: ['Zout','Water','Zuurstof','Helium'], correctIndex: 1 },
  { id: 7, text: 'In welk continent ligt Egypte grotendeels?', options: ['Europa','Azië','Afrika','Zuid-Amerika'], correctIndex: 2 },
  { id: 8, text: 'Wat is 7 x 8?', options: ['54','56','58','64'], correctIndex: 1 },
  { id: 9, text: 'Welke kleur krijg je door rood en wit te mengen?', options: ['Roze','Paars','Oranje','Bruin'], correctIndex: 0 },
  { id: 10, text: 'Wat meet je met een thermometer?', options: ['Snelheid','Lengte','Temperatuur','Gewicht'], correctIndex: 2 }
];

let currentIndex = 0;

// Event listener
submitBtn.addEventListener('click', handleSubmit);

function renderQuestion() {
  const q = questions[currentIndex];
  questionText.textContent = q.text;
  optionsContainer.innerHTML = '';
  feedback.className = 'feedback';
  feedback.textContent = '';
  answered = false;

  q.options.forEach((opt, i) => {
    const id = `opt_${q.id}_${i}`;
    const div = document.createElement('div');
    div.className = 'option';

    const input = document.createElement('input');
    input.type = 'radio';
    input.name = 'answer';
    input.id = id;
    input.value = i;

    const label = document.createElement('label');
    label.htmlFor = id;
    label.textContent = opt;

    div.appendChild(input);
    div.appendChild(label);
    optionsContainer.appendChild(div);
  });

  submitBtn.textContent = (currentIndex === questions.length -1) ? 'Beantwoord en bekijk resultaat' : 'Antwoord controleren';
}

function handleSubmit() {
  if (answered) return;
  const selected = document.querySelector('input[name="answer"]:checked');
  if (!selected) {
    feedback.textContent = 'Selecteer alstublieft een antwoord';
    feedback.className = 'feedback incorrect';
    return;
  }

  answered = true;
  const q = questions[currentIndex];
  const chosen = parseInt(selected.value, 10);
  const correct = q.correctIndex === chosen;
  if (correct) {
    totalScore++;
    scoreValue.textContent = totalScore;
    feedback.textContent = 'Correct!';
    feedback.className = 'feedback correct';
  } else {
    feedback.textContent = `Onjuist. Het juiste antwoord is: ${q.options[q.correctIndex]}`;
    feedback.className = 'feedback incorrect';
  }

  // Disable inputs
  document.querySelectorAll('input[name="answer"]').forEach(i => i.disabled = true);

  // If last question go to results after short delay
  if (currentIndex === questions.length -1) {
    // sla score op en navigeer
    localStorage.setItem('quizScore', String(totalScore));
    localStorage.setItem('quizTotal', String(questions.length));
    setTimeout(() => { window.location.href = 'results.html'; }, 900);
  } else {
    // ga naar volgende vraag na korte delay
    setTimeout(() => {
      currentIndex++;
      renderQuestion();
    }, 900);
  }
}

// Start
renderQuestion();