import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	RichText,
	InnerBlocks,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl, CheckboxControl, TextControl, Notice, Spinner, Button } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { useState } from '@wordpress/element';
import { decodeEntities } from '@wordpress/html-entities';
import metadata from './block.json';
import './style.scss';
import './editor.scss';

const TEMPLATE = [
	[ 'rehab/faq-item', { question: 'Is treatment confidential?', answer: 'Yes — we maintain strict privacy protocols and never disclose client information to third parties.' } ],
	[ 'rehab/faq-item', { question: 'How long is the program?', answer: 'Programs range from 4 to 12+ weeks depending on individual needs and treatment plan.' } ],
	[ 'rehab/faq-item', { question: 'Do you offer aftercare?', answer: 'Yes — every client receives a comprehensive aftercare plan with ongoing support after they leave.' } ],
];
const ALLOWED = [ 'rehab/faq-item' ];

/** FAQ CPT picker — searches `faq` posts by title and lets the editor toggle which IDs are referenced. */
function FaqPicker( { selectedIds, onChange } ) {
	const [ search, setSearch ] = useState( '' );
	const args = { per_page: 50, orderby: 'title', order: 'asc' };
	if ( search ) args.search = search;

	const { faqs, isResolving } = useSelect( ( select ) => {
		const { getEntityRecords, isResolving: r } = select( 'core' );
		return {
			faqs: getEntityRecords( 'postType', 'faq', args ) || [],
			isResolving: r( 'getEntityRecords', [ 'postType', 'faq', args ] ),
		};
	}, [ search ] );

	const toggle = ( id ) => {
		const exists = selectedIds.includes( id );
		const next = exists ? selectedIds.filter( ( x ) => x !== id ) : [ ...selectedIds, id ];
		onChange( next );
	};

	return (
		<>
			<TextControl
				label={ __( 'Search FAQs', 'rehab-blocks' ) }
				value={ search }
				onChange={ setSearch }
				placeholder={ __( 'Type to filter…', 'rehab-blocks' ) }
			/>
			{ isResolving && <Spinner /> }
			{ ! isResolving && faqs.length === 0 && (
				<Notice status="info" isDismissible={ false }>{ __( 'No FAQ records match.', 'rehab-blocks' ) }</Notice>
			) }
			<div style={ { maxHeight: '300px', overflowY: 'auto', border: '1px solid #e5e5e5', padding: '0.5rem', marginTop: '0.5rem' } }>
				{ faqs.map( ( faq ) => (
					<CheckboxControl
						key={ faq.id }
						label={ `#${ faq.id } — ${ decodeEntities( faq.title?.rendered || '(untitled)' ) }` }
						checked={ selectedIds.includes( faq.id ) }
						onChange={ () => toggle( faq.id ) }
					/>
				) ) }
			</div>
			{ selectedIds.length > 0 && (
				<p style={ { fontSize: '0.85em', marginTop: '0.5rem' } }>
					{ __( 'Selected order (drag in the order panel below):', 'rehab-blocks' ) } { selectedIds.join( ', ' ) }
				</p>
			) }
		</>
	);
}

/** Preview of FAQ records pulled by ID — shown in the editor canvas when cptIds is set. */
function FaqPreview( { ids } ) {
	const faqs = useSelect( ( select ) => {
		if ( ! ids || ids.length === 0 ) return [];
		const { getEntityRecord } = select( 'core' );
		return ids.map( ( id ) => getEntityRecord( 'postType', 'faq', id ) ).filter( Boolean );
	}, [ ids ] );

	if ( ! faqs.length ) return <Spinner />;
	return (
		<>
			{ faqs.map( ( faq ) => (
				<details key={ faq.id } className="rehab-faq-item">
					<summary className="rehab-faq-item__summary">
						<span>{ decodeEntities( faq.title?.rendered || '' ) }</span>
						<span className="rehab-faq-item__icon" aria-hidden="true"></span>
					</summary>
					<div className="rehab-faq-item__answer">
						<p>{ decodeEntities( ( faq.content?.rendered || '' ).replace( /<[^>]+>/g, '' ) ) }</p>
					</div>
				</details>
			) ) }
		</>
	);
}

function Edit( { attributes, setAttributes } ) {
	const { background, heading, cptIds = [] } = attributes;
	const blockProps = useBlockProps( {
		className: `rehab-faq rehab-bg-${ background }`,
	} );
	const isCpt = cptIds.length > 0;
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'FAQ settings', 'rehab-blocks' ) } initialOpen>
					<SelectControl
						label={ __( 'Background', 'rehab-blocks' ) }
						value={ background }
						options={ [
							{ label: 'White', value: 'white' },
							{ label: 'Cream', value: 'cream' },
							{ label: 'Sage mist', value: 'sage-mist' },
						] }
						onChange={ ( v ) => setAttributes( { background: v } ) }
					/>
				</PanelBody>
				<PanelBody title={ __( 'Pull from FAQ records', 'rehab-blocks' ) } initialOpen>
					<p style={ { fontSize: '0.85em', margin: '0 0 0.5rem' } }>
						{ __( 'Select FAQ records to render dynamically. When set, the inline FAQ items below are ignored and the live FAQ content is pulled at render time — editing a FAQ post propagates everywhere it\'s used.', 'rehab-blocks' ) }
					</p>
					<FaqPicker selectedIds={ cptIds } onChange={ ( ids ) => setAttributes( { cptIds: ids } ) } />
					{ cptIds.length > 0 && (
						<Button variant="link" isDestructive onClick={ () => setAttributes( { cptIds: [] } ) } style={ { marginTop: '0.5rem' } }>
							{ __( 'Clear selection (revert to inline FAQs)', 'rehab-blocks' ) }
						</Button>
					) }
				</PanelBody>
			</InspectorControls>
			<section { ...blockProps }>
				<div className="rehab-container rehab-container--narrow">
					<RichText
						tagName="h2"
						className="rehab-heading rehab-heading--lg rehab-faq__heading"
						value={ heading }
						onChange={ ( v ) => setAttributes( { heading: v } ) }
						placeholder={ __( 'FAQ heading…', 'rehab-blocks' ) }
						allowedFormats={ [ 'core/bold', 'core/italic' ] }
					/>
					<div className="rehab-faq__list">
						{ isCpt ? (
							<>
								<Notice status="info" isDismissible={ false }>
									{ __( 'Pulling from FAQ records: ', 'rehab-blocks' ) }{ cptIds.join( ', ' ) }
								</Notice>
								<FaqPreview ids={ cptIds } />
							</>
						) : (
							<InnerBlocks
								template={ TEMPLATE }
								allowedBlocks={ ALLOWED }
								renderAppender={ InnerBlocks.ButtonBlockAppender }
							/>
						) }
					</div>
				</div>
			</section>
		</>
	);
}

/**
 * Save returns the InnerBlocks content. The server-side render.php takes
 * over to produce the final HTML — emits CPT-sourced FAQs when cptIds is
 * set, otherwise falls back to the InnerBlocks-rendered content here.
 */
function save() {
	return <InnerBlocks.Content />;
}

registerBlockType( metadata.name, { edit: Edit, save } );
