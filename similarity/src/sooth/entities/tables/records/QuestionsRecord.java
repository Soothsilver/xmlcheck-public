/**
 * This class is generated by jOOQ
 */
package sooth.entities.tables.records;

/**
 * This class is generated by jOOQ.
 */
@javax.annotation.Generated(
	value = {
		"http://www.jooq.org",
		"jOOQ version:3.5.0"
	},
	comments = "This class is generated by jOOQ"
)
@java.lang.SuppressWarnings({ "all", "unchecked", "rawtypes" })
public class QuestionsRecord extends org.jooq.impl.UpdatableRecordImpl<sooth.entities.tables.records.QuestionsRecord> implements org.jooq.Record6<java.lang.Integer, java.lang.String, java.lang.String, java.lang.String, java.lang.String, java.lang.Integer> {

	private static final long serialVersionUID = 2041811969;

	/**
	 * Setter for <code>asmregen.questions.id</code>.
	 */
	public void setId(java.lang.Integer value) {
		setValue(0, value);
	}

	/**
	 * Getter for <code>asmregen.questions.id</code>.
	 */
	public java.lang.Integer getId() {
		return (java.lang.Integer) getValue(0);
	}

	/**
	 * Setter for <code>asmregen.questions.text</code>.
	 */
	public void setText(java.lang.String value) {
		setValue(1, value);
	}

	/**
	 * Getter for <code>asmregen.questions.text</code>.
	 */
	public java.lang.String getText() {
		return (java.lang.String) getValue(1);
	}

	/**
	 * Setter for <code>asmregen.questions.type</code>.
	 */
	public void setType(java.lang.String value) {
		setValue(2, value);
	}

	/**
	 * Getter for <code>asmregen.questions.type</code>.
	 */
	public java.lang.String getType() {
		return (java.lang.String) getValue(2);
	}

	/**
	 * Setter for <code>asmregen.questions.options</code>.
	 */
	public void setOptions(java.lang.String value) {
		setValue(3, value);
	}

	/**
	 * Getter for <code>asmregen.questions.options</code>.
	 */
	public java.lang.String getOptions() {
		return (java.lang.String) getValue(3);
	}

	/**
	 * Setter for <code>asmregen.questions.attachments</code>.
	 */
	public void setAttachments(java.lang.String value) {
		setValue(4, value);
	}

	/**
	 * Getter for <code>asmregen.questions.attachments</code>.
	 */
	public java.lang.String getAttachments() {
		return (java.lang.String) getValue(4);
	}

	/**
	 * Setter for <code>asmregen.questions.lectureId</code>.
	 */
	public void setLectureid(java.lang.Integer value) {
		setValue(5, value);
	}

	/**
	 * Getter for <code>asmregen.questions.lectureId</code>.
	 */
	public java.lang.Integer getLectureid() {
		return (java.lang.Integer) getValue(5);
	}

	// -------------------------------------------------------------------------
	// Primary key information
	// -------------------------------------------------------------------------

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Record1<java.lang.Integer> key() {
		return (org.jooq.Record1) super.key();
	}

	// -------------------------------------------------------------------------
	// Record6 type implementation
	// -------------------------------------------------------------------------

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Row6<java.lang.Integer, java.lang.String, java.lang.String, java.lang.String, java.lang.String, java.lang.Integer> fieldsRow() {
		return (org.jooq.Row6) super.fieldsRow();
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Row6<java.lang.Integer, java.lang.String, java.lang.String, java.lang.String, java.lang.String, java.lang.Integer> valuesRow() {
		return (org.jooq.Row6) super.valuesRow();
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Field<java.lang.Integer> field1() {
		return sooth.entities.tables.Questions.QUESTIONS.ID;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Field<java.lang.String> field2() {
		return sooth.entities.tables.Questions.QUESTIONS.TEXT;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Field<java.lang.String> field3() {
		return sooth.entities.tables.Questions.QUESTIONS.TYPE;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Field<java.lang.String> field4() {
		return sooth.entities.tables.Questions.QUESTIONS.OPTIONS;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Field<java.lang.String> field5() {
		return sooth.entities.tables.Questions.QUESTIONS.ATTACHMENTS;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Field<java.lang.Integer> field6() {
		return sooth.entities.tables.Questions.QUESTIONS.LECTUREID;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public java.lang.Integer value1() {
		return getId();
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public java.lang.String value2() {
		return getText();
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public java.lang.String value3() {
		return getType();
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public java.lang.String value4() {
		return getOptions();
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public java.lang.String value5() {
		return getAttachments();
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public java.lang.Integer value6() {
		return getLectureid();
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public QuestionsRecord value1(java.lang.Integer value) {
		setId(value);
		return this;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public QuestionsRecord value2(java.lang.String value) {
		setText(value);
		return this;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public QuestionsRecord value3(java.lang.String value) {
		setType(value);
		return this;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public QuestionsRecord value4(java.lang.String value) {
		setOptions(value);
		return this;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public QuestionsRecord value5(java.lang.String value) {
		setAttachments(value);
		return this;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public QuestionsRecord value6(java.lang.Integer value) {
		setLectureid(value);
		return this;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public QuestionsRecord values(java.lang.Integer value1, java.lang.String value2, java.lang.String value3, java.lang.String value4, java.lang.String value5, java.lang.Integer value6) {
		return this;
	}

	// -------------------------------------------------------------------------
	// Constructors
	// -------------------------------------------------------------------------

	/**
	 * Create a detached QuestionsRecord
	 */
	public QuestionsRecord() {
		super(sooth.entities.tables.Questions.QUESTIONS);
	}

	/**
	 * Create a detached, initialised QuestionsRecord
	 */
	public QuestionsRecord(java.lang.Integer id, java.lang.String text, java.lang.String type, java.lang.String options, java.lang.String attachments, java.lang.Integer lectureid) {
		super(sooth.entities.tables.Questions.QUESTIONS);

		setValue(0, id);
		setValue(1, text);
		setValue(2, type);
		setValue(3, options);
		setValue(4, attachments);
		setValue(5, lectureid);
	}
}
