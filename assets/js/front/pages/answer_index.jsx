import AnswerComponent from '../Answer/AnswerComponent'
import ReactDOM from 'react-dom'
import React from 'react'

const rootElement = document.querySelector('#questions-component')
const questions = JSON.parse(rootElement.dataset.questions)

if (questions.length > 0) {
  ReactDOM.render(<AnswerComponent questions={questions} submitUrl={rootElement.dataset.submitUrl} />, rootElement)
}
else {
  rootElement.innerHTML = `
    <p>No questions for the moment, Please refresh this screen in a few minutes.</p>
  `
}
