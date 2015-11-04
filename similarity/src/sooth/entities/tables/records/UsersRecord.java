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
public class UsersRecord extends org.jooq.impl.UpdatableRecordImpl<sooth.entities.tables.records.UsersRecord> implements org.jooq.Record15<java.lang.Integer, java.lang.String, java.lang.Integer, java.lang.String, java.lang.String, java.lang.String, java.sql.Timestamp, java.lang.String, java.lang.String, java.lang.String, java.sql.Timestamp, java.lang.Byte, java.lang.Byte, java.lang.Byte, java.lang.Byte> {

	private static final long serialVersionUID = 558262870;

	/**
	 * Setter for <code>asmregen.users.id</code>.
	 */
	public void setId(java.lang.Integer value) {
		setValue(0, value);
	}

	/**
	 * Getter for <code>asmregen.users.id</code>.
	 */
	public java.lang.Integer getId() {
		return (java.lang.Integer) getValue(0);
	}

	/**
	 * Setter for <code>asmregen.users.name</code>.
	 */
	public void setName(java.lang.String value) {
		setValue(1, value);
	}

	/**
	 * Getter for <code>asmregen.users.name</code>.
	 */
	public java.lang.String getName() {
		return (java.lang.String) getValue(1);
	}

	/**
	 * Setter for <code>asmregen.users.type</code>.
	 */
	public void setType(java.lang.Integer value) {
		setValue(2, value);
	}

	/**
	 * Getter for <code>asmregen.users.type</code>.
	 */
	public java.lang.Integer getType() {
		return (java.lang.Integer) getValue(2);
	}

	/**
	 * Setter for <code>asmregen.users.pass</code>.
	 */
	public void setPass(java.lang.String value) {
		setValue(3, value);
	}

	/**
	 * Getter for <code>asmregen.users.pass</code>.
	 */
	public java.lang.String getPass() {
		return (java.lang.String) getValue(3);
	}

	/**
	 * Setter for <code>asmregen.users.realName</code>.
	 */
	public void setRealname(java.lang.String value) {
		setValue(4, value);
	}

	/**
	 * Getter for <code>asmregen.users.realName</code>.
	 */
	public java.lang.String getRealname() {
		return (java.lang.String) getValue(4);
	}

	/**
	 * Setter for <code>asmregen.users.email</code>.
	 */
	public void setEmail(java.lang.String value) {
		setValue(5, value);
	}

	/**
	 * Getter for <code>asmregen.users.email</code>.
	 */
	public java.lang.String getEmail() {
		return (java.lang.String) getValue(5);
	}

	/**
	 * Setter for <code>asmregen.users.lastAccess</code>.
	 */
	public void setLastaccess(java.sql.Timestamp value) {
		setValue(6, value);
	}

	/**
	 * Getter for <code>asmregen.users.lastAccess</code>.
	 */
	public java.sql.Timestamp getLastaccess() {
		return (java.sql.Timestamp) getValue(6);
	}

	/**
	 * Setter for <code>asmregen.users.activationCode</code>.
	 */
	public void setActivationcode(java.lang.String value) {
		setValue(7, value);
	}

	/**
	 * Getter for <code>asmregen.users.activationCode</code>.
	 */
	public java.lang.String getActivationcode() {
		return (java.lang.String) getValue(7);
	}

	/**
	 * Setter for <code>asmregen.users.encryptionType</code>.
	 */
	public void setEncryptiontype(java.lang.String value) {
		setValue(8, value);
	}

	/**
	 * Getter for <code>asmregen.users.encryptionType</code>.
	 */
	public java.lang.String getEncryptiontype() {
		return (java.lang.String) getValue(8);
	}

	/**
	 * Setter for <code>asmregen.users.resetLink</code>.
	 */
	public void setResetlink(java.lang.String value) {
		setValue(9, value);
	}

	/**
	 * Getter for <code>asmregen.users.resetLink</code>.
	 */
	public java.lang.String getResetlink() {
		return (java.lang.String) getValue(9);
	}

	/**
	 * Setter for <code>asmregen.users.resetLinkExpiry</code>.
	 */
	public void setResetlinkexpiry(java.sql.Timestamp value) {
		setValue(10, value);
	}

	/**
	 * Getter for <code>asmregen.users.resetLinkExpiry</code>.
	 */
	public java.sql.Timestamp getResetlinkexpiry() {
		return (java.sql.Timestamp) getValue(10);
	}

	/**
	 * Setter for <code>asmregen.users.send_email_on_submission_rated</code>.
	 */
	public void setSendEmailOnSubmissionRated(java.lang.Byte value) {
		setValue(11, value);
	}

	/**
	 * Getter for <code>asmregen.users.send_email_on_submission_rated</code>.
	 */
	public java.lang.Byte getSendEmailOnSubmissionRated() {
		return (java.lang.Byte) getValue(11);
	}

	/**
	 * Setter for <code>asmregen.users.send_email_on_new_assignment</code>.
	 */
	public void setSendEmailOnNewAssignment(java.lang.Byte value) {
		setValue(12, value);
	}

	/**
	 * Getter for <code>asmregen.users.send_email_on_new_assignment</code>.
	 */
	public java.lang.Byte getSendEmailOnNewAssignment() {
		return (java.lang.Byte) getValue(12);
	}

	/**
	 * Setter for <code>asmregen.users.send_email_on_new_submission</code>.
	 */
	public void setSendEmailOnNewSubmission(java.lang.Byte value) {
		setValue(13, value);
	}

	/**
	 * Getter for <code>asmregen.users.send_email_on_new_submission</code>.
	 */
	public java.lang.Byte getSendEmailOnNewSubmission() {
		return (java.lang.Byte) getValue(13);
	}

	/**
	 * Setter for <code>asmregen.users.deleted</code>.
	 */
	public void setDeleted(java.lang.Byte value) {
		setValue(14, value);
	}

	/**
	 * Getter for <code>asmregen.users.deleted</code>.
	 */
	public java.lang.Byte getDeleted() {
		return (java.lang.Byte) getValue(14);
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
	// Record15 type implementation
	// -------------------------------------------------------------------------

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Row15<java.lang.Integer, java.lang.String, java.lang.Integer, java.lang.String, java.lang.String, java.lang.String, java.sql.Timestamp, java.lang.String, java.lang.String, java.lang.String, java.sql.Timestamp, java.lang.Byte, java.lang.Byte, java.lang.Byte, java.lang.Byte> fieldsRow() {
		return (org.jooq.Row15) super.fieldsRow();
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Row15<java.lang.Integer, java.lang.String, java.lang.Integer, java.lang.String, java.lang.String, java.lang.String, java.sql.Timestamp, java.lang.String, java.lang.String, java.lang.String, java.sql.Timestamp, java.lang.Byte, java.lang.Byte, java.lang.Byte, java.lang.Byte> valuesRow() {
		return (org.jooq.Row15) super.valuesRow();
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Field<java.lang.Integer> field1() {
		return sooth.entities.tables.Users.USERS.ID;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Field<java.lang.String> field2() {
		return sooth.entities.tables.Users.USERS.NAME;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Field<java.lang.Integer> field3() {
		return sooth.entities.tables.Users.USERS.TYPE;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Field<java.lang.String> field4() {
		return sooth.entities.tables.Users.USERS.PASS;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Field<java.lang.String> field5() {
		return sooth.entities.tables.Users.USERS.REALNAME;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Field<java.lang.String> field6() {
		return sooth.entities.tables.Users.USERS.EMAIL;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Field<java.sql.Timestamp> field7() {
		return sooth.entities.tables.Users.USERS.LASTACCESS;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Field<java.lang.String> field8() {
		return sooth.entities.tables.Users.USERS.ACTIVATIONCODE;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Field<java.lang.String> field9() {
		return sooth.entities.tables.Users.USERS.ENCRYPTIONTYPE;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Field<java.lang.String> field10() {
		return sooth.entities.tables.Users.USERS.RESETLINK;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Field<java.sql.Timestamp> field11() {
		return sooth.entities.tables.Users.USERS.RESETLINKEXPIRY;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Field<java.lang.Byte> field12() {
		return sooth.entities.tables.Users.USERS.SEND_EMAIL_ON_SUBMISSION_RATED;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Field<java.lang.Byte> field13() {
		return sooth.entities.tables.Users.USERS.SEND_EMAIL_ON_NEW_ASSIGNMENT;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Field<java.lang.Byte> field14() {
		return sooth.entities.tables.Users.USERS.SEND_EMAIL_ON_NEW_SUBMISSION;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public org.jooq.Field<java.lang.Byte> field15() {
		return sooth.entities.tables.Users.USERS.DELETED;
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
	public java.lang.Integer value3() {
		return getType();
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public java.lang.String value4() {
		return getPass();
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public java.lang.String value5() {
		return getRealname();
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public java.lang.String value6() {
		return getEmail();
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public java.sql.Timestamp value7() {
		return getLastaccess();
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public java.lang.String value8() {
		return getActivationcode();
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public java.lang.String value9() {
		return getEncryptiontype();
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public java.lang.String value10() {
		return getResetlink();
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public java.sql.Timestamp value11() {
		return getResetlinkexpiry();
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public java.lang.Byte value12() {
		return getSendEmailOnSubmissionRated();
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public java.lang.Byte value13() {
		return getSendEmailOnNewAssignment();
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public java.lang.Byte value14() {
		return getSendEmailOnNewSubmission();
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public java.lang.Byte value15() {
		return getDeleted();
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public UsersRecord value1(java.lang.Integer value) {
		setId(value);
		return this;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public UsersRecord value2(java.lang.String value) {
		setName(value);
		return this;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public UsersRecord value3(java.lang.Integer value) {
		setType(value);
		return this;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public UsersRecord value4(java.lang.String value) {
		setPass(value);
		return this;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public UsersRecord value5(java.lang.String value) {
		setRealname(value);
		return this;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public UsersRecord value6(java.lang.String value) {
		setEmail(value);
		return this;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public UsersRecord value7(java.sql.Timestamp value) {
		setLastaccess(value);
		return this;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public UsersRecord value8(java.lang.String value) {
		setActivationcode(value);
		return this;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public UsersRecord value9(java.lang.String value) {
		setEncryptiontype(value);
		return this;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public UsersRecord value10(java.lang.String value) {
		setResetlink(value);
		return this;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public UsersRecord value11(java.sql.Timestamp value) {
		setResetlinkexpiry(value);
		return this;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public UsersRecord value12(java.lang.Byte value) {
		setSendEmailOnSubmissionRated(value);
		return this;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public UsersRecord value13(java.lang.Byte value) {
		setSendEmailOnNewAssignment(value);
		return this;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public UsersRecord value14(java.lang.Byte value) {
		setSendEmailOnNewSubmission(value);
		return this;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public UsersRecord value15(java.lang.Byte value) {
		setDeleted(value);
		return this;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public UsersRecord values(java.lang.Integer value1, java.lang.String value2, java.lang.Integer value3, java.lang.String value4, java.lang.String value5, java.lang.String value6, java.sql.Timestamp value7, java.lang.String value8, java.lang.String value9, java.lang.String value10, java.sql.Timestamp value11, java.lang.Byte value12, java.lang.Byte value13, java.lang.Byte value14, java.lang.Byte value15) {
		return this;
	}

	// -------------------------------------------------------------------------
	// Constructors
	// -------------------------------------------------------------------------

	/**
	 * Create a detached UsersRecord
	 */
	public UsersRecord() {
		super(sooth.entities.tables.Users.USERS);
	}

	/**
	 * Create a detached, initialised UsersRecord
	 */
	public UsersRecord(java.lang.Integer id, java.lang.String name, java.lang.Integer type, java.lang.String pass, java.lang.String realname, java.lang.String email, java.sql.Timestamp lastaccess, java.lang.String activationcode, java.lang.String encryptiontype, java.lang.String resetlink, java.sql.Timestamp resetlinkexpiry, java.lang.Byte sendEmailOnSubmissionRated, java.lang.Byte sendEmailOnNewAssignment, java.lang.Byte sendEmailOnNewSubmission, java.lang.Byte deleted) {
		super(sooth.entities.tables.Users.USERS);

		setValue(0, id);
		setValue(1, name);
		setValue(2, type);
		setValue(3, pass);
		setValue(4, realname);
		setValue(5, email);
		setValue(6, lastaccess);
		setValue(7, activationcode);
		setValue(8, encryptiontype);
		setValue(9, resetlink);
		setValue(10, resetlinkexpiry);
		setValue(11, sendEmailOnSubmissionRated);
		setValue(12, sendEmailOnNewAssignment);
		setValue(13, sendEmailOnNewSubmission);
		setValue(14, deleted);
	}
}