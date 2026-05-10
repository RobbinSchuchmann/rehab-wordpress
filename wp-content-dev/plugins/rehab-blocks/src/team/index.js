import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	RichText,
	InnerBlocks,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';
import './editor.scss';

const TEMPLATE = [
	[ 'rehab/team-member' ],
	[ 'rehab/team-member' ],
	[ 'rehab/team-member' ],
	[ 'rehab/team-member' ],
];
const ALLOWED = [ 'rehab/team-member' ];

function Edit( { attributes, setAttributes } ) {
	const { background, columns, heading } = attributes;
	const blockProps = useBlockProps( {
		className: `rehab-team rehab-bg-${ background } rehab-team--cols-${ columns }`,
	} );
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Team settings', 'rehab-blocks' ) } initialOpen>
					<SelectControl
						label={ __( 'Columns (desktop)', 'rehab-blocks' ) }
						value={ String( columns ) }
						options={ [
							{ label: '2 columns', value: '2' },
							{ label: '3 columns', value: '3' },
							{ label: '4 columns', value: '4' },
							{ label: '5 columns', value: '5' },
						] }
						onChange={ ( v ) => setAttributes( { columns: parseInt( v, 10 ) } ) }
					/>
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
			</InspectorControls>
			<section { ...blockProps }>
				<div className="rehab-container">
					<header className="rehab-team__header">
						<RichText
							tagName="h2"
							className="rehab-heading rehab-heading--lg"
							value={ heading }
							onChange={ ( v ) => setAttributes( { heading: v } ) }
							placeholder={ __( 'Section heading…', 'rehab-blocks' ) }
							allowedFormats={ [ 'core/bold', 'core/italic' ] }
						/>
					</header>
					<div className="rehab-team__grid">
						<InnerBlocks
							template={ TEMPLATE }
							allowedBlocks={ ALLOWED }
							renderAppender={ InnerBlocks.ButtonBlockAppender }
						/>
					</div>
				</div>
			</section>
		</>
	);
}

function save( { attributes } ) {
	const { background, columns, heading } = attributes;
	const blockProps = useBlockProps.save( {
		className: `rehab-team rehab-bg-${ background } rehab-team--cols-${ columns }`,
	} );
	return (
		<section { ...blockProps }>
			<div className="rehab-container">
				{ heading && (
					<header className="rehab-team__header">
						<RichText.Content
							tagName="h2"
							className="rehab-heading rehab-heading--lg"
							value={ heading }
						/>
					</header>
				) }
				<div className="rehab-team__grid">
					<InnerBlocks.Content />
				</div>
			</div>
		</section>
	);
}

registerBlockType( metadata.name, { edit: Edit, save } );
