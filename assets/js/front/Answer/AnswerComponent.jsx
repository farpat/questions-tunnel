import React, { useState } from 'react'
import PropTypes from 'prop-types'

/**
 * Main component
 *
 * @param {Array<{label: string, suggestions: Array<string>}>} questions
 * @param {string} submitUrl
 * @return {JSX.Element}
 * @constructor
 */
function AnswerComponent ({ questions, submitUrl }) {
  const [alert, setAlert] = useState({ type: 'danger', message: '' })
  const [index, setIndex] = useState(0)
  const [answers, setAnswers] = useState({})
  const currentQuestion = questions[index]
  const isIndexAtLastQuestion = index === questions.length - 1

  return (
    <>
      <Breadcrumb
        questions={questions}
        currentIndex={index}
        setCurrentIndex={setIndex}
      />

      <section>
        <Alert alert={alert} />

        <h2>{currentQuestion.label}</h2>

        {/* Question Field */}
        <Field
          question={currentQuestion}
          index={index}
          answers={answers}
          setAnswers={setAnswers}
        />

        {/* Answer submission */}
        <NextButton
          setAlert={setAlert}
          alert={alert}
          isLastQuestion={isIndexAtLastQuestion}
          setIndex={setIndex}
          index={index}
          answers={answers}
          submitUrl={submitUrl}
        />
      </section>
    </>
  )
}

/**
 *
 * @param {{type: string, message: string}} alert
 * @constructor
 */
function Alert ({ alert }) {
  if (alert.message !== '') {
    return (
      <div className={'alert alert-' + alert.type}>
        {alert.message}
      </div>
    )
  }

  return null
}

/**
 * Next button or submit button
 * @param {function} setAlert
 * @param {boolean} isLastQuestion
 * @param {function} setIndex
 * @param {number} index
 * @param {Object<number, string>} answers
 * @param {string} submitUrl
 * @return {JSX.Element}
 * @constructor
 */
function NextButton ({ setAlert, isLastQuestion, setIndex, index, answers, submitUrl }) {
  let onClick

  if (isLastQuestion) {
    onClick = async () => {
      const data = new FormData
      for (let key in answers) {
        data.append(`answers[${key}]`, answers[key])
      }

      const response = await fetch(submitUrl, {
        method: 'POST',
        body: data
      })

      const responseData = await response.json()

      if (responseData.status === 'OK') {
        window.location.href = '/'
      } else {
        setAlert({ type: 'danger', message: responseData.message })
      }
    }

    return (
      <button className="btn btn-danger" onClick={onClick}>
        Submit your answer
      </button>
    )
  } else {
    onClick = () => setIndex(index + 1)

    return (
      <button className="btn btn-primary" onClick={onClick}>
        Next &rarr;
      </button>
    )
  }
}

/**
 * Input or radio buttons
 * @param {{label: string, suggestions: Array<string>}} question
 * @param {number} index
 * @param {Object<number, string>} answers
 * @param {function} setAnswers
 * @return {JSX.Element}
 * @constructor
 */
function Field ({ question, index, answers, setAnswers }) {
  const hasSuggestions = question.suggestions.length > 0
  const currentAnswer = answers[question.label]
  let Component

  if (hasSuggestions) {
    const onChange = suggestion => {
      setAnswers({
        ...answers,
        [question.label]: suggestion
      })
    }

    Component = question.suggestions.map(suggestion => {
      const isChecked = currentAnswer === suggestion

      return (
        <div key={suggestion} className="form-check mb-3">
          <input
            className="form-check-input"
            type="radio"
            name="flexRadioDefault"
            checked={isChecked}
            id={'flexRadioDefault-' + suggestion}
            onChange={() => onChange(suggestion)}
          />
          <label
            className="form-check-label"
            htmlFor={'flexRadioDefault-' + suggestion}
          >
            {suggestion}
          </label>
        </div>
      )
    })
  } else {
    const onInput = event => {
      setAnswers({
        ...answers,
        [question.label]: event.target.value
      })
    }

    Component = (
      <div className="mb-3">
        <label htmlFor="exampleFormControlInput1" className="form-label">
          Email address
        </label>
        <input
          type="email"
          className="form-control"
          id="exampleFormControlInput1"
          placeholder="name@example.com"
          value={currentAnswer || ''}
          onInput={onInput}
        />
      </div>
    )
  }

  return Component
}

/**
 * Breadcrumb navigation
 * @param {Array<{label: string, suggestions: Array<string>}>} questions
 * @param {number} currentIndex
 * @param {function} setCurrentIndex
 * @return {JSX.Element}
 * @constructor
 */
function Breadcrumb ({ questions, currentIndex, setCurrentIndex }) {
  return (
    <nav aria-label="Questions breadcrumb">
      <ol className="steps">
        {questions.map((question, index) => {
          const isCurrent = currentIndex === index
          let breadcrumbItemClass, onClick, title

          if (isCurrent) {
            breadcrumbItemClass = 'step active'
            onClick = null
            title = null
          } else {
            breadcrumbItemClass = 'step'
            onClick = () => {
              setCurrentIndex(index)
            }
            title = question.label
          }

          return (
            <li key={question.label} title={title} onClick={onClick} className={breadcrumbItemClass}>
              <span className="step-body">{index + 1}</span>
            </li>
          )
        })}
      </ol>
    </nav>
  )
}

AnswerComponent.propTypes = {
  questions: PropTypes.arrayOf(
    PropTypes.exact({
      label: PropTypes.string.isRequired,
      suggestions: PropTypes.arrayOf(PropTypes.string)
    })
  ),
  submitUrl: PropTypes.string.isRequired
}

export default AnswerComponent
