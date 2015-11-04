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
public class AttachmentsRecord extends org.jooq.impl.UpdatableRecordImpl<sooth.entities.tables.records.AttachmentsRecord> implements org.jooq.Record5<java.lang.Integer, java.lang.String, java.lang.String, java.lang.String, java.lang.Integer> {

	private static final long serialVersionUID = -950788801;

	/**
	 * Setter for <code>asmregen.attachments.id</code>.
	 */
	public void setId(java.lang.Integer value) {
		setValue(0, value);
	}

	/**
	 * Getter for <code>asmregen.attachments.id</code>.
	 */
	public java.lang.Integer getId() {
		return (java.lang.Integer) getValue(0);
	}

	/**
	 * Setter for <code>asmregen.attachments.name</code>.
	 */
	public void setName(java.lang.String value) {
		setValue(1, value);
	}

	/**
	 * Getter for <code>asmregen.attachments.name</code>.
	 */
	public java.lang.String getName() {
		return (java.lang.String) getValue(1);
	}

	/**
	 * Setter for <code>asmregen.attachments.type</code>.
	 */
	public void setType(java.lang.String value) {
		setValue(2, value);
	}

	/**
	 * Getter for <code>asmregen.attachments.type</code>.
	 */
	public java.lang.String getType() {
		return (java.lang.String) getValue(2);
	}

	/**
	 * Setter for <code>asmregen.attachments.file</code>.
	 */
	public void setFile(java.lang.String value) {
		setValue(3, value);
	}

	/**
	 * Getter for <code>asmregen.attachments.file</code>.
	 */
	public java.lang.String getFile() {
		return (java.lang.String) getValue(3);
	}

	/**
	 * Setter for <code>asmregen.attachments.lectureId</code>.
	 */
	public void setLectureid(java.lang.Integer value) {
		setValue(4, value);
	}

	/**
	 * Getter for <code>asmregen.attachments.lectureId</code>.
	 */
	public java.lang.Integer getLectureid() {
		return (java.lang.Integer) getValue(4);
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
	// Record5 type implementation
	// -------------------------------------------------------------------------

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Row5<java.lang.Integer, java.lang.String, java.lang.String, java.lang.String, java.lang.Integer> fieldsRow() {
		return (org.jooq.Row5) super.fieldsRow();
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Row5<java.lang.Integer, java.lang.String, java.lang.String, java.lang.String, java.lang.Integer> valuesRow() {
		return (org.jooq.Row5) super.valuesRow();
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Field<java.lang.Integer> field1() {
		return sooth.entities.tables.Attachments.ATTACHMENTS.ID;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Field<java.lang.String> field2() {
		return sooth.entities.tables.Attachments.ATTACHMENTS.NAME;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Field<java.lang.String> field3() {
		return sooth.entities.tables.Attachments.ATTACHMENTS.TYPE;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Field<java.lang.String> field4() {
		return sooth.entities.tables.Attachments.ATTACHMENTS.FILE;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Field<java.lang.Integer> field5() {
		return sooth.entities.tables.Attachments.ATTACHMENTS.LECTUREID;
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
		return getName();
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
		return getFile();
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public java.lang.Integer value5() {
		return getLectureid();
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public AttachmentsRecord value1(java.lang.Integer value) {
		setId(value);
		return this;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public AttachmentsRecord value2(java.lang.String value) {
		setName(value);
		return this;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public AttachmentsRecord value3(java.lang.String value) {
		setType(value);
		return this;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public AttachmentsRecord value4(java.lang.String value) {
		setFile(value);
		return this;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public AttachmentsRecord value5(java.lang.Integer value) {
		setLectureid(value);
		return this;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public AttachmentsRecord values(java.lang.Integer value1, java.lang.String value2, java.lang.String value3, java.lang.String value4, java.lang.Integer value5) {
		return this;
	}

	// -------------------------------------------------------------------------
	// Constructors
	// -------------------------------------------------------------------------

	/**
	 * Create a detached AttachmentsRecord
	 */
	public AttachmentsRecord() {
		super(sooth.entities.tables.Attachments.ATTACHMENTS);
	}

	/**
	 * Create a detached, initialised AttachmentsRecord
	 */
	public AttachmentsRecord(java.lang.Integer id, java.lang.String name, java.lang.String type, java.lang.String file, java.lang.Integer lectureid) {
		super(sooth.entities.tables.Attachments.ATTACHMENTS);

		setValue(0, id);
		setValue(1, name);
		setValue(2, type);
		setValue(3, file);
		setValue(4, lectureid);
	}
}
